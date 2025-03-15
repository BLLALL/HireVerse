SELECT 'CREATE DATABASE hireverse_test'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'hireverse_test')\gexec
