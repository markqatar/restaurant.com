<?php
class Category {
    private $pdo;
    public function __construct($pdo){ $this->pdo=$pdo; $this->migrate(); }
    private function migrate(){
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS blog_category_translations (id INT AUTO_INCREMENT PRIMARY KEY, category_id INT NOT NULL, language_code VARCHAR(10) NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(150) NULL, description TEXT NULL, meta_title VARCHAR(160) NULL, meta_description VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, UNIQUE KEY uniq_cat_lang(category_id,language_code), KEY slug(slug), FOREIGN KEY(category_id) REFERENCES blog_categories(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        // migrate legacy columns into default & ar translations
        try {
            $cols = $this->pdo->query("SHOW COLUMNS FROM blog_categories")->fetchAll(PDO::FETCH_COLUMN);
            $defaultLang = $this->getDefaultPublicLanguage();
            if(in_array('name',$cols)){
                $ins=$this->pdo->prepare("INSERT IGNORE INTO blog_category_translations (category_id,language_code,name,slug,description) VALUES (?,?,?,?,?)");
                $rows=$this->pdo->query("SELECT id,name,slug,description,name_ar,description_ar FROM blog_categories")->fetchAll(PDO::FETCH_ASSOC);
                foreach($rows as $r){
                    if($r['name']){ $slug = $r['slug'] ?: $this->generateSlug($r['name'],'', $r['id']); $ins->execute([$r['id'],$defaultLang,$r['name'],$slug,$r['description']]); }
                    if(isset($r['name_ar']) && $r['name_ar']){ $slugAr = $this->generateSlug($r['name_ar'],'ar',$r['id']); $ins->execute([$r['id'],'ar',$r['name_ar'],$slugAr,$r['description_ar']]); }
                }
            }
        } catch(Exception $e){ /* silent */ }
    }
    private function getDefaultPublicLanguage(){ try { return get_default_public_language_from_db(); } catch(Exception $e){ return 'en'; } }
    public function getAllCategories($onlyActive = null){
        try {
            $lang = $this->getDefaultPublicLanguage();
            $sql = "SELECT c.*, t.name, t.slug, COUNT(a.id) article_count FROM blog_categories c LEFT JOIN blog_category_translations t ON t.category_id=c.id AND t.language_code=? LEFT JOIN blog_articles a ON a.category_id=c.id AND a.status='published'".
                ($onlyActive!==null?" WHERE c.is_active=".(int)$onlyActive:'')." GROUP BY c.id ORDER BY c.sort_order, t.name";
            $st=$this->pdo->prepare($sql); $st->execute([$lang]); return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){ error_log($e->getMessage()); return []; }
    }
    public function getCategoryById($id){
        $lang=$this->getDefaultPublicLanguage();
        $st=$this->pdo->prepare("SELECT c.*, t.name, t.slug, t.description FROM blog_categories c LEFT JOIN blog_category_translations t ON t.category_id=c.id AND t.language_code=? WHERE c.id=?");
        $st->execute([$lang,$id]); $row=$st->fetch(PDO::FETCH_ASSOC); if($row){ $row['translations']=$this->getTranslations($id);} return $row;
    }
    public function getCategoryBySlug($slug, $lang=null){
        $lang = $lang ?: $this->getDefaultPublicLanguage();
        $st=$this->pdo->prepare("SELECT c.*, t.name, t.description FROM blog_categories c INNER JOIN blog_category_translations t ON t.category_id=c.id WHERE t.slug=? AND t.language_code=? AND c.is_active=1 LIMIT 1");
        $st->execute([$slug,$lang]); return $st->fetch(PDO::FETCH_ASSOC);
    }
    public function createCategory($baseData, $translations){
        try {
            $st=$this->pdo->prepare("INSERT INTO blog_categories (parent_id, sort_order, is_active, created_at) VALUES (?,?,?,NOW())");
            $st->execute([$baseData['parent_id']??null,$baseData['sort_order']??0, ($baseData['is_active']??1)?1:0]);
            $id=$this->pdo->lastInsertId();
            $this->saveTranslations($id,$translations);
            return $id;
        } catch(PDOException $e){ error_log($e->getMessage()); return false; }
    }
    public function updateCategory($id,$baseData,$translations){
        try {
            $st=$this->pdo->prepare("UPDATE blog_categories SET parent_id=?, sort_order=?, is_active=? WHERE id=?");
            $st->execute([$baseData['parent_id']??null,$baseData['sort_order']??0, ($baseData['is_active']??1)?1:0,$id]);
            $this->saveTranslations($id,$translations,true);
            return true;
        } catch(PDOException $e){ error_log($e->getMessage()); return false; }
    }
    public function deleteCategory($id){
        try {
            $this->pdo->prepare("UPDATE blog_articles SET category_id=NULL WHERE category_id=?")->execute([$id]);
            return $this->pdo->prepare("DELETE FROM blog_categories WHERE id=?")->execute([$id]);
        } catch(PDOException $e){ error_log($e->getMessage()); return false; }
    }
    public function generateSlug($name,$language_code='', $excludeId=null){
        $base=strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/','-',$name),'-')); $slug=$base?:uniqid('cat'); $i=1; while($this->slugExists($slug,$language_code,$excludeId)){ $slug=$base.'-'.$i++; } return $slug; }
    private function slugExists($slug,$lang,$excludeId=null){ $sql="SELECT COUNT(*) FROM blog_category_translations WHERE slug=? AND language_code=?"; $p=[$slug,$lang]; if($excludeId){ $sql.=" AND category_id<>?"; $p[]=$excludeId; } $st=$this->pdo->prepare($sql); $st->execute($p); return $st->fetchColumn()>0; }
    private function getTranslations($categoryId){ $st=$this->pdo->prepare("SELECT language_code,name,slug,description,meta_title,meta_description FROM blog_category_translations WHERE category_id=?"); $st->execute([$categoryId]); $rows=$st->fetchAll(PDO::FETCH_ASSOC); $out=[]; foreach($rows as $r){ $out[$r['language_code']]=$r; } return $out; }
    private function saveTranslations($categoryId,$translations,$update=false){ foreach($translations as $lang=>$t){ $name=trim($t['name']??''); if($name==='') continue; $slug=$t['slug']??$this->generateSlug($name,$lang,$categoryId); $desc=$t['description']??null; $meta_title=$t['meta_title']??null; $meta_desc=$t['meta_description']??null; $sql="INSERT INTO blog_category_translations (category_id,language_code,name,slug,description,meta_title,meta_description) VALUES (:id,:lang,:name,:slug,:description,:mt,:md) ON DUPLICATE KEY UPDATE name=VALUES(name), slug=VALUES(slug), description=VALUES(description), meta_title=VALUES(meta_title), meta_description=VALUES(meta_description)"; $st=$this->pdo->prepare($sql); $st->execute([':id'=>$categoryId,':lang'=>$lang,':name'=>$name,':slug'=>$slug,':description'=>$desc,':mt'=>$meta_title,':md'=>$meta_desc]); } }
}
?>