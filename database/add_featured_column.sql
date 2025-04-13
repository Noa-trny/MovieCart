ALTER TABLE movies ADD COLUMN featured BOOLEAN DEFAULT FALSE;

-- Mettre à jour quelques films pour qu'ils soient en vedette
UPDATE movies SET featured = TRUE WHERE id IN (1, 2, 3); 