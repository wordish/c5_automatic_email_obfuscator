<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));

class AutomaticEmailObfuscatorPackage extends Package {

	const PKG_HANDLE = 'automatic_email_obfuscator';

	protected $pkgHandle = 'automatic_email_obfuscator';
	protected $appVersionRequired = '5.4.0';
	protected $pkgVersion = '1.2.3';
	
	private static $_helper = null;
	
	public function getPackageDescription() {
		return t("Automatically obfuscates all the e-mail addresses on your site to a form that most spambots cannot read.");
	}
	
	public function getPackageName() {
		return t("Automatic Email Obfuscator");
	}
	
	public function install() {
		$pkg = parent::install();
	}
	
	public function on_start() {
		// Page attributes not available for $p
		// If you need the attributes add the commented row.
		$req = Request::get();
		$p = $req->getRequestedPage();
		//$p = Page::getByID($p->getCollectionID());
		if (!$p->isAdminArea()) {
			// This is also loaded for tool urls
			// Might be good in some cases, although not needed in most of them (@dashboard)
			self::$_helper = Loader::helper("email_obfuscation", $this->pkgHandle);
			
			// Noticed strange behavior with on_page_output event in some cases
			// This does not work 100% of the cases with that event
			$file = DIR_PACKAGES . '/' . $this->pkgHandle . '/controller.php';
			Events::extend('on_before_render', 'AutomaticEmailObfuscatorPackage', 'on_before_render', $file);
			Events::extend('on_render_complete', 'AutomaticEmailObfuscatorPackage', 'on_render_complete', $file);
			Events::extend('on_page_view', 'AutomaticEmailObfuscatorPackage', 'on_page_view', $file);
		}
	}
	
	public function on_before_render() {
		ob_start();
	}
	
	public function on_render_complete() {
		$output = ob_get_contents();
		ob_end_clean();
		echo self::on_page_output($output);
	}
	
	public function on_page_output($output) {
		return self::$_helper->findAndObfuscate($output);
	}
	
	public function on_page_view() {
		self::$_helper->on_page_view();
	}

}