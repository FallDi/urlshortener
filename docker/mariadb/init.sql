CREATE DATABASE IF NOT EXISTS app;

-- User for usage by database migrations only with wide grants
CREATE USER IF NOT EXISTS 'app-migrations'@'%' IDENTIFIED BY 'app-migrations-s3cr3t-pwd';
GRANT ALL PRIVILEGES ON app.* TO 'app-migrations'@'%';

-- User for usage from backend source code (limited grants)
CREATE USER IF NOT EXISTS 'app-backend'@'%' IDENTIFIED BY 'app-backend-s3cr3t-pwd';
GRANT SELECT, INSERT, UPDATE, DELETE ON app.* TO 'app-backend'@'%';

FLUSH PRIVILEGES;
