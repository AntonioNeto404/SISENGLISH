-- Add classificacao column to docentes table
ALTER TABLE docentes ADD COLUMN classificacao VARCHAR(100) NULL DEFAULT NULL;
