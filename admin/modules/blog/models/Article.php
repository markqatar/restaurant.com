<?php
class Article {
	private $pdo;
	public function __construct($pdo){ $this->pdo=$pdo; $this->migrate(); }
	private function migrate(){
		$this->pdo->exec("CREATE TABLE IF NOT EXISTS blog_article_tags (article_id INT NOT NULL, tag VARCHAR(50) NOT NULL, PRIMARY KEY(article_id,tag), INDEX(tag), FOREIGN KEY(article_id) REFERENCES blog_articles(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$this->pdo->exec("CREATE TABLE IF NOT EXISTS blog_article_revisions (id INT AUTO_INCREMENT PRIMARY KEY, article_id INT NOT NULL, title VARCHAR(200), content LONGTEXT, excerpt TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(article_id) REFERENCES blog_articles(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
	$this->pdo->exec("CREATE TABLE IF NOT EXISTS blog_article_translations (id INT AUTO_INCREMENT PRIMARY KEY, article_id INT NOT NULL, language_code VARCHAR(10) NOT NULL, title VARCHAR(200) NOT NULL, slug VARCHAR(200) NULL, content LONGTEXT NULL, excerpt TEXT NULL, meta_title VARCHAR(255) NULL, meta_description VARCHAR(255) NULL, meta_keywords VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, UNIQUE KEY uniq_article_lang(article_id,language_code), KEY slug (slug), FOREIGN KEY(article_id) REFERENCES blog_articles(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$cols = $this->pdo->query("SHOW COLUMNS FROM blog_articles")->fetchAll(PDO::FETCH_COLUMN);
		$alter=[];
		if(!in_array('status',$cols)) $alter[]="ADD COLUMN status ENUM('draft','published','private') DEFAULT 'draft' AFTER is_published";
		if(!in_array('author_id',$cols)) $alter[]="ADD COLUMN author_id INT NULL AFTER category_id";
		if(!in_array('views',$cols)) $alter[]="ADD COLUMN views INT NOT NULL DEFAULT 0 AFTER author_id";
		if(!in_array('is_featured',$cols)) $alter[]="ADD COLUMN is_featured TINYINT(1) DEFAULT 0 AFTER views";
		if(!in_array('meta_title',$cols)) $alter[]="ADD COLUMN meta_title VARCHAR(255) NULL AFTER is_featured";
		if(!in_array('meta_description',$cols)) $alter[]="ADD COLUMN meta_description VARCHAR(255) NULL AFTER meta_title";
		if(!in_array('meta_keywords',$cols)) $alter[]="ADD COLUMN meta_keywords VARCHAR(255) NULL AFTER meta_description";
		if(!in_array('updated_at',$cols)) $alter[]="ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at";
		if($alter){ $this->pdo->exec('ALTER TABLE blog_articles '.implode(',',$alter)); }
		// Migration of existing base content into translation table (default language) if not already migrated
		try {
			$defaultLang = $this->getDefaultPublicLanguage();
			$rows = $this->pdo->query("SELECT id,title,slug,content,excerpt,meta_title,meta_description,meta_keywords FROM blog_articles WHERE title IS NOT NULL AND title<>''")->fetchAll(PDO::FETCH_ASSOC);
			$ins = $this->pdo->prepare("INSERT IGNORE INTO blog_article_translations (article_id,language_code,title,slug,content,excerpt,meta_title,meta_description,meta_keywords) VALUES (?,?,?,?,?,?,?,?,?)");
			foreach($rows as $r){
				$slug = $r['slug'] ?: $this->generateSlug($r['title']);
				$ins->execute([$r['id'],$defaultLang,$r['title'],$slug,$r['content'],$r['excerpt'],$r['meta_title'],$r['meta_description'],$r['meta_keywords']]);
			}
			// Arabic legacy
			if(in_array('title_ar',$cols)){
				$arRows = $this->pdo->query("SELECT id,title_ar AS title, content_ar AS content, excerpt_ar AS excerpt, meta_title_ar AS meta_title, meta_description_ar AS meta_description, meta_keywords FROM blog_articles WHERE title_ar IS NOT NULL AND title_ar<>''")->fetchAll(PDO::FETCH_ASSOC);
				foreach($arRows as $r){
					$slug = $this->generateSlug($r['title']);
					$ins->execute([$r['id'],'ar',$r['title'],$slug,$r['content'],$r['excerpt'],$r['meta_title'],$r['meta_description'],$r['meta_keywords']]);
				}
			}
		} catch(Exception $e){ /* silent */ }
	}
	public function generateSlug(string $title, ?int $excludeId=null){
		$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/','-',$title),'-'));
		if($slug==='') $slug = uniqid('post');
		$base=$slug; $i=1;
		while($this->slugExists($slug,$excludeId)){$slug=$base.'-'.$i++;}
		return $slug;
	}
	private function slugExists(string $slug, ?int $excludeId){
		$sql='SELECT COUNT(*) FROM blog_articles WHERE slug=?'; $params=[$slug];
		if($excludeId){ $sql.=' AND id<>?'; $params[]=$excludeId; }
		$st=$this->pdo->prepare($sql); $st->execute($params); return $st->fetchColumn()>0;
	}
	private function translationSlugExists(string $slug,string $lang, ?int $excludeArticleId=null){
		$sql='SELECT COUNT(*) FROM blog_article_translations WHERE slug=? AND language_code=?'; $params=[$slug,$lang];
		if($excludeArticleId){ $sql.=' AND article_id<>?'; $params[]=$excludeArticleId; }
		$st=$this->pdo->prepare($sql); $st->execute($params); return $st->fetchColumn()>0;
	}
	public function ensureUniqueTranslationSlug(string $desired, string $lang, ?int $excludeArticleId=null){
		$slug=strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/','-',$desired),'-')) ?: uniqid('post');
		$base=$slug; $i=2; while($this->translationSlugExists($slug,$lang,$excludeArticleId)){ $slug=$base.'-'.$i++; }
		return $slug;
	}
	public function isTranslationSlugAvailable(string $slug,string $lang, ?int $excludeArticleId=null){
		if($slug==='') return false; return !$this->translationSlugExists($slug,$lang,$excludeArticleId);
	}
	public function createArticle(array $data){
		$st=$this->pdo->prepare("INSERT INTO blog_articles (title,slug,content,excerpt,featured_image,category_id,is_published,published_at,status,author_id,is_featured,meta_title,meta_description,meta_keywords) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$ok=$st->execute([
			$data['title'],$data['slug'],$data['content'],$data['excerpt']??null,$data['featured_image']??null,$data['category_id']??null,
			($data['status']??'draft')==='published'?1:0,$data['published_at']??null,$data['status']??'draft',$data['author_id']??null,$data['is_featured']??0,$data['meta_title']??null,$data['meta_description']??null,$data['meta_keywords']??null
		]);
		if($ok){
			$id=$this->pdo->lastInsertId();
			$this->saveTags($id,$data['tags']??[]);
			// Save translation (default language)
			$lang = $data['language'] ?? $this->getDefaultPublicLanguage();
			$this->upsertTranslation($id,$lang,[
				'title'=>$data['title'],'slug'=>$data['slug'],'content'=>$data['content'],'excerpt'=>$data['excerpt']??null,
				'meta_title'=>$data['meta_title']??null,'meta_description'=>$data['meta_description']??null,'meta_keywords'=>$data['meta_keywords']??null
			]);
			return $id; }
		return false;
	}
	public function updateArticle(int $id,array $data){
		$this->createRevision($id);
		// Distinguish between base article update (default language) and translation-only upsert
		$updatingBase = empty($data['language']) || $data['language']===$this->getDefaultPublicLanguage();
		if($updatingBase){
			$st=$this->pdo->prepare("UPDATE blog_articles SET title=?, slug=?, content=?, excerpt=?, featured_image=?, category_id=?, is_published=?, published_at=?, status=?, is_featured=?, meta_title=?, meta_description=?, meta_keywords=? WHERE id=?");
			$ok=$st->execute([
				$data['title'],$data['slug'],$data['content'],$data['excerpt']??null,$data['featured_image']??null,$data['category_id']??null,
				($data['status']??'draft')==='published'?1:0,$data['published_at']??null,$data['status']??'draft',$data['is_featured']??0,$data['meta_title']??null,$data['meta_description']??null,$data['meta_keywords']??null,$id
			]);
			if(!$ok) return false;
			$this->saveTags($id,$data['tags']??[]);
		}
		$lang = $data['language'] ?? $this->getDefaultPublicLanguage();
		// If override fields present (for non-default translations), prefer them
		$title = $data['title_override'] ?? $data['title'];
		$slug = $data['slug_override'] ?? $data['slug'];
		$slug = $this->ensureUniqueTranslationSlug($slug,$lang,$id);
		$content = $data['content_override'] ?? $data['content'];
		extract(['excerpt'=>$data['excerpt_override'] ?? ($data['excerpt']??null)]);
		$meta_title = $data['meta_title_override'] ?? ($data['meta_title']??null);
		$meta_description = $data['meta_description_override'] ?? ($data['meta_description']??null);
		$meta_keywords = $data['meta_keywords_override'] ?? ($data['meta_keywords']??null);
		$this->upsertTranslation($id,$lang,[
			'title'=>$title,'slug'=>$slug,'content'=>$content,'excerpt'=>$excerpt,
			'meta_title'=>$meta_title,'meta_description'=>$meta_description,'meta_keywords'=>$meta_keywords
		]);
		return true;
	}
	private function createRevision(int $id){
		$row=$this->getArticleById($id); if(!$row) return; $st=$this->pdo->prepare("INSERT INTO blog_article_revisions (article_id,title,content,excerpt) VALUES (?,?,?,?)"); $st->execute([$id,$row['title'],$row['content'],$row['excerpt']]);
	}
	public function getArticleById(int $id){
	$defaultLang = $this->getDefaultPublicLanguage();
	$st=$this->pdo->prepare("SELECT a.*, c.name AS category_name, u.name AS author_name, t.title, t.slug, t.content, t.excerpt, t.meta_title, t.meta_description, t.meta_keywords FROM blog_articles a LEFT JOIN blog_categories c ON c.id=a.category_id LEFT JOIN users u ON u.id=a.author_id LEFT JOIN blog_article_translations t ON t.article_id=a.id AND t.language_code=? WHERE a.id=?");
	$st->execute([$defaultLang,$id]); $row=$st->fetch(PDO::FETCH_ASSOC); if($row){ $row['tags']=$this->getTags($id); $row['translations']=$this->getTranslations($id);} return $row;
	}
	public function deleteArticle(int $id){ $st=$this->pdo->prepare("DELETE FROM blog_articles WHERE id=?"); return $st->execute([$id]); }
	public function getAllArticles(){
		$defaultLang = $this->getDefaultPublicLanguage();
		$sql="SELECT a.*, c.name AS category_name, u.name AS author_name, t.title, t.slug, t.excerpt FROM blog_articles a LEFT JOIN blog_categories c ON c.id=a.category_id LEFT JOIN users u ON u.id=a.author_id LEFT JOIN blog_article_translations t ON t.article_id=a.id AND t.language_code=? ORDER BY a.id DESC LIMIT 500";
		$st=$this->pdo->prepare($sql); $st->execute([$defaultLang]); $rows=$st->fetchAll(PDO::FETCH_ASSOC); foreach($rows as &$r){ $r['tags']=$this->getTags($r['id']); } return $rows;
	}
	public function incrementViews(int $id){ $this->pdo->prepare("UPDATE blog_articles SET views=views+1 WHERE id=?")->execute([$id]); }
	private function saveTags(int $articleId,array $tags){ $this->pdo->prepare("DELETE FROM blog_article_tags WHERE article_id=?")->execute([$articleId]); $tags=array_unique(array_filter(array_map('trim',$tags))); $st=$this->pdo->prepare("INSERT INTO blog_article_tags (article_id,tag) VALUES (?,?)"); foreach($tags as $t){ if($t!==''){ $st->execute([$articleId,substr($t,0,50)]); }} }
	private function getTags(int $articleId){ $st=$this->pdo->prepare("SELECT tag FROM blog_article_tags WHERE article_id=? ORDER BY tag"); $st->execute([$articleId]); return array_column($st->fetchAll(PDO::FETCH_ASSOC),'tag'); }
	public function datatable(int $start,int $length,string $search='', string $statusFilter='', int $categoryFilter=0){
		$defaultLang=$this->getDefaultPublicLanguage();
		$clauses=[]; $params=[':lang'=>$defaultLang];
		if($search!==''){ $clauses[]="(t.title LIKE :s OR t.slug LIKE :s OR c.name LIKE :s OR u.name LIKE :s)"; $params[':s']='%'.$search.'%'; }
		if($statusFilter!==''){ $clauses[]='a.status = :status'; $params[':status']=$statusFilter; }
		if($categoryFilter>0){ $clauses[]='a.category_id = :cid'; $params[':cid']=$categoryFilter; }
		$where = $clauses? ('WHERE '.implode(' AND ',$clauses)) : '';
		$total = $this->pdo->query('SELECT COUNT(*) FROM blog_articles')->fetchColumn();
		$countSql = "SELECT COUNT(*) FROM blog_articles a LEFT JOIN blog_article_translations t ON t.article_id=a.id AND t.language_code=:lang LEFT JOIN blog_categories c ON c.id=a.category_id LEFT JOIN users u ON u.id=a.author_id $where";
		$st=$this->pdo->prepare($countSql); $st->execute($params); $filtered=$st->fetchColumn();
		$sql="SELECT a.*, t.title, t.slug, t.excerpt, c.name AS category_name, u.name AS author_name FROM blog_articles a LEFT JOIN blog_article_translations t ON t.article_id=a.id AND t.language_code=:lang LEFT JOIN blog_categories c ON c.id=a.category_id LEFT JOIN users u ON u.id=a.author_id $where ORDER BY a.id DESC LIMIT :start,:len";
		$st2=$this->pdo->prepare($sql); foreach($params as $k=>$v){ $st2->bindValue($k,$v);} $st2->bindValue(':start',$start,PDO::PARAM_INT); $st2->bindValue(':len',$length,PDO::PARAM_INT); $st2->execute();
		$rows=$st2->fetchAll(PDO::FETCH_ASSOC); foreach($rows as &$r){ $r['tags']=$this->getTags($r['id']); }
		return ['total'=>$total,'filtered'=>$filtered,'rows'=>$rows];
	}
	private function upsertTranslation(int $articleId,string $lang,array $t){
		if(!isset($t['title'])||trim($t['title'])==='') return; $slug=$t['slug']?:$this->generateSlug($t['title'],$articleId); $sql="INSERT INTO blog_article_translations (article_id,language_code,title,slug,content,excerpt,meta_title,meta_description,meta_keywords) VALUES (:id,:lang,:title,:slug,:content,:excerpt,:meta_title,:meta_description,:meta_keywords) ON DUPLICATE KEY UPDATE title=VALUES(title), slug=VALUES(slug), content=VALUES(content), excerpt=VALUES(excerpt), meta_title=VALUES(meta_title), meta_description=VALUES(meta_description), meta_keywords=VALUES(meta_keywords)"; $st=$this->pdo->prepare($sql); $st->execute([':id'=>$articleId,':lang'=>$lang,':title'=>$t['title'],':slug'=>$slug,':content'=>$t['content']??null,':excerpt'=>$t['excerpt']??null,':meta_title'=>$t['meta_title']??null,':meta_description'=>$t['meta_description']??null,':meta_keywords'=>$t['meta_keywords']??null]); }
	private function getTranslations(int $articleId){ $st=$this->pdo->prepare("SELECT language_code,title,slug,excerpt,meta_title,meta_description,meta_keywords FROM blog_article_translations WHERE article_id=?"); $st->execute([$articleId]); $rows=$st->fetchAll(PDO::FETCH_ASSOC); $out=[]; foreach($rows as $r){ $out[$r['language_code']]=$r; } return $out; }
	private function getDefaultPublicLanguage(){ try { return get_default_public_language_from_db(); } catch(Exception $e){ return 'en'; } }
	public function listRevisions(int $articleId,int $limit=20){ $st=$this->pdo->prepare("SELECT id,title,created_at FROM blog_article_revisions WHERE article_id=? ORDER BY id DESC LIMIT ?"); $st->bindValue(1,$articleId,PDO::PARAM_INT); $st->bindValue(2,$limit,PDO::PARAM_INT); $st->execute(); return $st->fetchAll(PDO::FETCH_ASSOC); }
	public function restoreRevision(int $revisionId){ $st=$this->pdo->prepare("SELECT * FROM blog_article_revisions WHERE id=?"); $st->execute([$revisionId]); $rev=$st->fetch(PDO::FETCH_ASSOC); if(!$rev) return false; $this->createRevision($rev['article_id']); $up=$this->pdo->prepare("UPDATE blog_articles SET title=?, content=?, excerpt=? WHERE id=?"); $ok=$up->execute([$rev['title'],$rev['content'],$rev['excerpt'],$rev['article_id']]); if($ok){ // also update translation default lang
			$lang=$this->getDefaultPublicLanguage();
			$this->upsertTranslation($rev['article_id'],$lang,[ 'title'=>$rev['title'],'slug'=>'','content'=>$rev['content'],'excerpt'=>$rev['excerpt']]); }
		return $ok; }
}
?>
