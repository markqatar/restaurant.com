-- Add avatar column to user_preferences table
ALTER TABLE user_preferences ADD COLUMN avatar VARCHAR(255) DEFAULT 'default.png' AFTER language;

-- Update existing records to have default avatar
UPDATE user_preferences SET avatar = 'default.png' WHERE avatar IS NULL;