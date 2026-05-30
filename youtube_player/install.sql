CREATE TABLE IF NOT EXISTS yt_videos (
  id INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  youtube_url TEXT NOT NULL,
  youtube_id VARCHAR(20) NOT NULL,
  slug VARCHAR(80) NOT NULL,
  description TEXT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_slug (slug),
  KEY idx_active_sort (is_active, sort_order, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS yt_settings (
  setting_key VARCHAR(80) NOT NULL,
  setting_value TEXT NOT NULL,
  PRIMARY KEY (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO yt_settings(setting_key, setting_value) VALUES
('site_title','교육 영상 재생관'),
('max_videos','6'),
('autoplay','1'),
('mute','0'),
('show_list','1')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
