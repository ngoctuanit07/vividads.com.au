<?php 
function getGalleryMenu(AsiaConnect_Gallery_Model_Album $album, $level=0, $last=false)
{
	$html ='';
	$aClass='';
	if($album->getStatus())
	{
		$html .="<li";
		$hasChildren = $album->getChildrenAlbumInStore();
        if ($hasChildren) {
             $html.= ' onmouseover="toggleMenu(this,1)" onmouseout="toggleMenu(this,0)"';
        }
		$html.= ' class="level'.$level;
	    if ($last) {
            $html .= ' last';
        }
        if($album->getAlbumId()==1){
        	$html .= ' level-top';
        	$aClass = ' level-top';
        }
        $cnt = ($album->getAlbumId()==1) ? 1:0;
	    if ($hasChildren) {
            foreach ($hasChildren as $child) {
                if ($child->getStatus()) {
                    $cnt++;
                }
            }
        }
		if ($cnt > 0) {
			$html .= ' parent';
        }
        $html.= '">'."\n";
        $html.= '<a href="'.$album->getUrlRewrite().'" class="'.$aClass.'"><span>'.$album->getTitle().'</span></a>'."\n";
        if ($hasChildren){

            $j = 0;
            $htmlChildren = '';
            foreach ($hasChildren as $child) {
                if ($child->getStatus()) {
                    $htmlChildren.= getGalleryMenu($child, $level+1, ++$j >= $cnt);
                }
            }

            if (!empty($htmlChildren) or $album->getAlbumId()==1) {
                $html.= '<ul class="level' . $level ;
	            if($album->getAlbumId()==1){
	        		$html .= ' level-top';
	        	}
                $html .= '">'."\n"
                        .$htmlChildren
                        .'</ul>';
            }

        }
        $html.= '</li>'."\n";
        return $html;
	}
}
?>
<?php $_menu = $this->renderCategoriesMenuHtml(0,'level-top');?>
<div class="nav-container">
        <ul id="nav">
        <?php if($_menu){
        	echo($_menu);
        }
        $rootAlbum = Mage::getModel('gallery/album')->load(1);
        if(Mage::getStoreConfig("gallery/info/menu_enabled"))
        {        	
        	echo getGalleryMenu($rootAlbum, 0, true);
        }
        ?>        
        </ul>
    <?php echo $this->getChildHtml('topLeftLinks') ?>
</div>