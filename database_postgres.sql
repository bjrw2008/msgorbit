-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id SERIAL PRIMARY KEY,
    phone VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bots table
CREATE TABLE IF NOT EXISTS bots (
    id SERIAL PRIMARY KEY,
    bot_token VARCHAR(255) NOT NULL,
    bot_username VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Channels table
CREATE TABLE IF NOT EXISTS channels (
    id SERIAL PRIMARY KEY,
    bot_id INTEGER NOT NULL REFERENCES bots(id) ON DELETE CASCADE,
    channel_name VARCHAR(255) NOT NULL,
    channel_username VARCHAR(100),
    chat_id VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts table
CREATE TABLE IF NOT EXISTS posts (
    id SERIAL PRIMARY KEY,
    channel_id INTEGER NOT NULL REFERENCES channels(id) ON DELETE CASCADE,
    message TEXT NOT NULL,
    scheduled_time TIMESTAMP NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for performance
CREATE INDEX idx_posts_scheduled_time ON posts(scheduled_time, status);
CREATE INDEX idx_posts_status ON posts(status);
CREATE INDEX idx_channels_bot_id ON channels(bot_id);

-- Insert default admin (phone: 1234567890, password: admin123)
INSERT INTO admin_users (phone, password, full_name) 
VALUES ('0795564155', '$2a$12$n7eiQJzZR0xXS/Fj0tor..Spse9mZC5/vydP4tb1f2Z./MMm.psb.', 'Admin User')
ON CONFLICT (phone) DO NOTHING;

-- Create updated_at trigger function
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Add triggers for updated_at
CREATE TRIGGER update_admin_users_updated_at BEFORE UPDATE ON admin_users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_bots_updated_at BEFORE UPDATE ON bots
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_channels_updated_at BEFORE UPDATE ON channels
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_posts_updated_at BEFORE UPDATE ON posts
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
