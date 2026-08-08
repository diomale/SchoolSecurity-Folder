#!/usr/bin/env bash
set -e

echo "=== CCSS Database Initialization ==="

# SQL files are in /docker-entrypoint-initdb.d/sql/
SQL_DIR="/docker-entrypoint-initdb.d/sql"

# --- Create secondary database ---
echo "Creating securitysystemdatabase..."
mariadb -u root -p"$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS \`securitysystemdatabase\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    GRANT ALL PRIVILEGES ON \`securitysystemdatabase\`.* TO '$MYSQL_USER'@'%';
EOSQL
echo "securitysystemdatabase created."

# --- Import primary database schema (ccsecurity_db) ---
if [ -f "$SQL_DIR/ccsecurity_db.sql" ]; then
    echo "Importing ccsecurity_db schema..."
    mariadb -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < "$SQL_DIR/ccsecurity_db.sql"
    echo "ccsecurity_db imported."
else
    echo "WARNING: ccsecurity_db.sql not found, skipping."
fi

# --- Import secondary database schema (securitysystemdatabase) ---
if [ -f "$SQL_DIR/SecuritySystemDatabase.sql" ]; then
    echo "Importing securitysystemdatabase schema..."
    mariadb -u root -p"$MYSQL_ROOT_PASSWORD" securitysystemdatabase < "$SQL_DIR/SecuritySystemDatabase.sql"
    echo "securitysystemdatabase imported."
else
    echo "WARNING: SecuritySystemDatabase.sql not found, skipping."
fi

# --- Insert super admin ---
if [ -f "$SQL_DIR/insert-superadmin.sql" ]; then
    echo "Inserting super admin..."
    mariadb -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < "$SQL_DIR/insert-superadmin.sql"
    echo "Super admin inserted."
fi

# --- Apply schema changes that post-date the SecuritySystemDatabase.sql dump ---
if [ -f "$SQL_DIR/apply-missing-migrations.sql" ]; then
    echo "Applying post-dump schema changes to securitysystemdatabase..."
    mariadb -u root -p"$MYSQL_ROOT_PASSWORD" securitysystemdatabase < "$SQL_DIR/apply-missing-migrations.sql"
    echo "Post-dump schema changes applied."
fi

# --- Grant second DB privileges ---
if [ -f "$SQL_DIR/grant-second-db.sql" ]; then
    echo "Granting second DB privileges..."
    mariadb -u root -p"$MYSQL_ROOT_PASSWORD" < "$SQL_DIR/grant-second-db.sql"
    echo "Privileges granted."
fi

echo "=== Database initialization complete ==="
