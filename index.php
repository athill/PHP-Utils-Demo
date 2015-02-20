<?php
require_once('setup.inc.php');
$local = [
	'layout'=>[
		'leftsidebar'=> [['type'=>'content', 'content'=>'left side bar']],
		'rightsidebar'=> [['type'=>'content', 'content'=>'right side bar']],
	],
];

$page = new \Athill\Utils\Page($local);


//// move to heading
$menu = new \Athill\Utils\MenuUtils('/nested/nest2/nest2.2/nest2.2.1.php');
$breadcrumbs = $menu->getBreadcrumbs();
// $h->pa($breadcrumbs);
$h->onav('id="breadcrumbs"');
$lastbc = count($breadcrumbs) - 1;
$delim = '&gt;';
$h->otag('ul');
//// TODO: should this be a list? handled through js/css?
foreach ($breadcrumbs as $i => $breadcrumb){
	if ($i == $lastbc) {
		$h->li($breadcrumb['display']);
	} else {
		$h->li($h->rtn('a', [$breadcrumb['href'], $breadcrumb['display']]));
	}


}
$h->ctag('ul');
$h->cnav('/#breadcrumbs');
$h->onav('id="top-menu" class="clearfix"');
$menu->renderMenu();
$h->cnav('.#top-menu');
//// content
$h->h2($site['pagetitle']);
$h->p('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sit amet tellus ultricies, malesuada ex non, porta odio. Nunc elementum bibendum vestibulum. Cras commodo tincidunt libero at suscipit. Vestibulum suscipit justo in pellentesque blandit. Vivamus hendrerit est lobortis eros aliquet bibendum. Ut sodales diam nibh, et tempor magna posuere ac. In tristique efficitur ornare. Phasellus pretium, sapien eu placerat vehicula, felis ex placerat sapien, vel venenatis felis felis non leo. Vivamus lorem lacus, consequat id euismod interdum, mollis a augue. ');




////// generate directory structure
$menu = json_decode(file_get_contents('menu.json'), true);
$template = <<<EOT
	&lt;?php
EOT;


// require_once(%s.'setup.inc.php');
// \$local = [
// ];

// \$page = new \Athill\Utils\Page(\$local);

// \$page->end();

// $template = '';
generateFileStructure(['menu'=>$menu, 'template'=>$template]);
$page->end();



function generateFileStructure($options=[]) {
	global $h, $site;
	$defaults = [
		'template'=>'',
		//// these change with recursion
		'menu'=>'',
		'currdepth' => 0,
		'buildpath'=>''
	];
	$options = $h->extend($defaults, $options);
	foreach ($options['menu'] as $entry) {
		$href = $options['buildpath'].$entry['href'];
		if ($href != '/') {
			$filepath = $site['fileroot'].$href;
			if (preg_match('/\.php$/', $href)) {
				$h->tbr('file: '.$filepath);
				$h->tbr(sprintf($options['template'], str_repeat('../', $options['currdepth'])));

			} else {
				$h->tbr('dir: '.$filepath);
				$h->tbr(sprintf($options['template'], str_repeat('../', $options['currdepth'])));				
				if (isset($entry['children'])) {
					$change = $h->extend($options, [
						'menu'=>$entry['children'],
						'currdepth'=>$options['currdepth']+1,
						'buildpath'=>$options['buildpath'].$entry['href']
					]);
					generateFileStructure($change);
				} 					
			}
			
		}

	}
}
