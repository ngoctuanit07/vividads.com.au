<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Social Booster extension
 *
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @author     MageWorx Dev Team
 */

class MageWorx_SocialBooster_Helper_Bookmarks extends Mage_Core_Helper_Abstract
{
    protected $_origUrl;
    protected $_shortUrl;
    protected $_title;
    protected $_shortTitle;
    protected $_summary;
    protected $_siteName;
    protected $_autoIncrement = 0;
    

    protected $_bookmarks = array(
        'scriptstyle'=>array(
            'title'=>'Script &amp; Style',
            'share'=>'Submit this to %s',
            'url'=>'http://scriptandstyle.com/submit?url=ORIG_URL&amp;title=TITLE',
        ),
        'blinklist'=>array(
            'title'=>'Blinklist',
            'share'=>'Share this on %s',
            'url'=>'http://www.blinklist.com/index.php?Action=Blink/addblink.php&amp;Url=ORIG_URL&amp;Title=TITLE',
        ),
        'delicious'=>array(
            'title'=>'Delicious',
            'share'=>'Share this on %s',
            'url'=>'http://del.icio.us/post?url=ORIG_URL&amp;title=TITLE',
        ),
        'digg'=>array(
            'title'=>'Digg',
            'share'=>'Digg this!',
            'url'=>'http://digg.com/submit?phase=2&amp;url=ORIG_URL&amp;title=TITLE',
        ),
        'diigo'=>array(
            'title'=>'Diigo',
            'share'=>'Post this on %s',
            'url'=>'http://www.diigo.com/post?url=ORIG_URL&amp;title=TITLE&amp;desc=SUMMARY',
        ),
        'reddit'=>array(
            'title'=>'Reddit',
            'share'=>'Share this on %s',
            'url'=>'http://reddit.com/submit?url=ORIG_URL&amp;title=TITLE',
        ),
        /*'yahoobuzz'=>array(
            'title'=>'Yahoo! Buzz',
            'share'=>'Buzz up!',
            'url'=>'http://buzz.yahoo.com/submit/?submitUrl=ORIG_URL&amp;submitHeadline=TITLE&amp;submitSummary=YAHOOTEASER&amp;submitCategory=YAHOOCATEGORY&amp;submitAssetType=YAHOOMEDIATYPE',
        ),*/
        'stumbleupon'=>array(
            'title'=>'StumbleUpon',
            'share'=>'Stumble upon something good? Share it on %s',
            'url'=>'http://www.stumbleupon.com/submit?url=ORIG_URL&amp;title=TITLE',
        ),
        'technorati'=>array(
            'title'=>'Technorati',
            'share'=>'Share this on %s',
            'url'=>'http://technorati.com/faves?add=ORIG_URL',
        ),
        'mixx'=>array(
            'title'=>'Mixx',
            'share'=>'Share this on %s',
            'url'=>'http://www.mixx.com/submit?page_url=ORIG_URL&amp;title=TITLE',
        ),
        'myspace'=>array(
            'title'=>'MySpace',
            'share'=>'Post this to %s',
            'url'=>'http://www.myspace.com/Modules/PostTo/Pages/?u=ORIG_URL&amp;t=TITLE',
        ),
        'designfloat'=>array(
            'title'=>'DesignFloat',
            'share'=>'Submit this to %s',
            'url'=>'http://www.designfloat.com/submit.php?url=ORIG_URL&amp;title=TITLE',
        ),
        'facebook'=>array(
            'title'=>'Facebook',
            'share'=>'Share this on %s',
            'url'=>'http://www.facebook.com/share.php?v=4&amp;src=bm&amp;u=ORIG_URL&amp;t=TITLE',
        ),
        'twitter'=>array(
            'title'=>'Twitter',
            'share'=>'Tweet This!',
            'url'=>'http://twitter.com/home?status=SHORT_TITLE+-+SHORT_URL',
        ),
        /*'mail'=>array(
            'title'=>'"Email to a Friend" link',
            'share'=>'Email this to a friend?',
            'url'=>'mailto:?subject=%22TITLE%22&amp;body=I%20thought%20this%20article%20might%20interest%20you.%0A%0A%22SUMMARY%22%0A%0AYou%20can%20read%20the%20full%20article%20here%3A%20ORIG_URL',
        ),*/
        /*'tomuse'=>array(
            'title'=>'ToMuse',
            'share'=>'Suggest this article to %s',
            'url'=>'mailto:tips@tomuse.com?subject=New%20tip%20submitted%20via%20the%20SexyBookmarks%20Plugin!&amp;body=I%20would%20like%20to%20submit%20this%20article%3A%20%22TITLE%22%20for%20possible%20inclusion%20on%20ToMuse.%0A%0A%22SUMMARY%22%0A%0AYou%20can%20read%20the%20full%20article%20here%3A%20ORIG_URL',
        ),*/
        /*'comfeed'=>array(
            'title'=>'a \'Subscribe to Comments\' link',
            'share'=>'Subscribe to the comments for this post?',
            'url'=>'ORIG_URL',
        ),*/
        'linkedin'=>array(
            'title'=>'Linkedin',
            'share'=>'Share this on %s',
            'url'=>'http://www.linkedin.com/shareArticle?mini=true&amp;url=ORIG_URL&amp;title=TITLE&amp;summary=SUMMARY&amp;source=SITE_NAME',
        ),
        'newsvine'=>array(
            'title'=>'Newsvine',
            'share'=>'Seed this on %s',
            'url'=>'http://www.newsvine.com/_tools/seed&amp;save?u=ORIG_URL&amp;h=TITLE',
        ),
        'devmarks'=>array(
            'title'=>'Devmarks',
            'share'=>'Share this on %s',
            'url'=>'http://devmarks.com/index.php?posttext=SUMMARY&amp;posturl=ORIG_URL&amp;posttitle=TITLE',
        ),
        
        'misterwong'=>array(
            'title'=>'Mister Wong',
            'share'=>'Add this to %s',
            'url'=>'http://www.mister-wong.com/addurl/?bm_url=ORIG_URL&amp;bm_description=TITLE&amp;plugin=MageWorx+Social+Booster',
        ),
        'izeby'=>array(
            'title'=>'Izeby',
            'share'=>'Add this to %s',
            'url'=>'http://izeby.com/submit.php?url=ORIG_URL',
        ),
        'tipd'=>array(
            'title'=>'Tipd',
            'share'=>'Share this on %s',
            'url'=>'http://tipd.com/submit.php?url=ORIG_URL',
        ),
        'pfbuzz'=>array(
            'title'=>'PFBuzz',
            'share'=>'Share this on %s',
            'url'=>'http://pfbuzz.com/submit?url=ORIG_URL&amp;title=TITLE',
        ),
        'friendfeed'=>array(
            'title'=>'FriendFeed',
            'share'=>'Share this on %s',
            'url'=>'http://www.friendfeed.com/share?title=TITLE&amp;link=ORIG_URL',
        ),
        'blogmarks'=>array(
            'title'=>'BlogMarks',
            'share'=>'Mark this on %s',
            'url'=>'http://blogmarks.net/my/new.php?mini=1&amp;simple=1&amp;url=ORIG_URL&amp;title=TITLE',
        ),
        /*'twittley'=>array(
            'title'=>'Twittley',
            'share'=>'Submit this to %s',
            'url'=>'http://twittley.com/submit/?title=TITLE&amp;url=ORIG_URL&amp;desc=SUMMARY&amp;pcat=TWITT_CAT&amp;tags=DEFAULT_TAGS',
        ),*/
        'fwisp'=>array(
            'title'=>'Fwisp',
            'share'=>'Share this on %s',
            'url'=>'http://fwisp.com/submit?url=ORIG_URL',
        ),
        'designmoo'=>array(
            'title'=>'DesignMoo',
            'share'=>'Moo this on %s!',
            'url'=>'http://designmoo.com/submit?url=ORIG_URL&amp;title=TITLE&amp;body=SUMMARY',
        ),
        'bobrdobr'=>array(
            'title'=>'BobrDobr (Russian)',
            'share'=>'Share this on %s',
            'url'=>'http://bobrdobr.ru/addext.html?url=ORIG_URL&amp;title=TITLE',
        ),
        'yandex'=>array(
            'title'=>'Yandex.Bookmarks (Russian)',
            'share'=>'Add this to %s',
            'url'=>'http://zakladki.yandex.ru/userarea/links/addfromfav.asp?bAddLink_x=1&amp;lurl=ORIG_URL&amp;lname=TITLE',
        ),
        'memoryru'=>array(
            'title'=>'Memory.ru (Russian)',
            'share'=>'Add this to %s',
            'url'=>'http://memori.ru/link/?sm=1&amp;u_data[url]=ORIG_URL&amp;u_data[name]=TITLE',
        ),
        '100zakladok'=>array(
            'title'=>'100 bookmarks (Russian)',
            'share'=>'Add this to %s',
            'url'=>'http://www.100zakladok.ru/save/?bmurl=ORIG_URL&amp;bmtitle=TITLE',
        ),
        'moemesto'=>array(
            'title'=>'MyPlace (Russian)',
            'share'=>'Add this to %s',
            'url'=>'http://moemesto.ru/post.php?url=ORIG_URL&amp;title=TITLE',
        ),
        'hackernews'=>array(
            'title'=>'Hacker News',
            'share'=>'Submit this to %s',
            'url'=>'http://news.ycombinator.com/submitlink?u=ORIG_URL&amp;t=TITLE',
        ),
        'printfriendly'=>array(
            'title'=>'Print Friendly',
            'share'=>'Send this page to %s',
            'url'=>'http://www.printfriendly.com/print?url=ORIG_URL',
        ),
        'designbump'=>array(
            'title'=>'Design Bump',
            'share'=>'Bump this on %s',
            'url'=>'http://designbump.com/submit?url=ORIG_URL&amp;title=TITLE&amp;body=SUMMARY',
        ),
        'ning'=>array(
            'title'=>'Ning',
            'share'=>'Add this to %s',
            'url'=>'http://bookmarks.ning.com/addItem.php?url=ORIG_URL&amp;T=TITLE',
        ),
        'identica'=>array(
            'title'=>'Identica',
            'share'=>'Post this to %s',
            'url'=>'http://identi.ca//index.php?action=newnotice&amp;status_textarea=Browsing:+&quot;SHORT_TITLE&quot;+-+from+SHORT_URL',
        ),
        'xerpi'=>array(
            'title'=>'Xerpi',
            'share'=>'Save this to %s',
            'url'=>'http://www.xerpi.com/block/add_link_from_extension?url=ORIG_URL&amp;title=TITLE',
        ),
        'wikio'=>array(
            'title'=>'Wikio',
            'share'=>'Share this on %s',
            'url'=>'http://www.wikio.com/sharethis?url=ORIG_URL&amp;title=TITLE',
        ),
        'techmeme'=>array(
            'title'=>'TechMeme',
            'share'=>'Tip this to %s',
            'url'=>'http://twitter.com/home/?status=Tip+@Techmeme+ORIG_URL+&quot;TITLE&quot;',
        ),
        'sphinn'=>array(
            'title'=>'Sphinn',
            'share'=>'Sphinn this on %s',
            'url'=>'http://sphinn.com/index.php?c=post&amp;m=submit&amp;link=ORIG_URL',
        ),
        'posterous'=>array(
            'title'=>'Posterous',
            'share'=>'Post this to %s',
            'url'=>'http://posterous.com/share?linkto=ORIG_URL&amp;title=TITLE&amp;selection=SUMMARY',
        ),
        'globalgrind'=>array(
            'title'=>'Global Grind',
            'share'=>'Grind this! on %s',
            'url'=>'http://globalgrind.com/submission/submit.aspx?url=ORIG_URL&amp;type=Article&amp;title=TITLE',
        ),
        'pingfm'=>array(
            'title'=>'Ping.fm',
            'share'=>'Ping this on %s',
            'url'=>'http://ping.fm/ref/?link=ORIG_URL&amp;title=TITLE&amp;body=SUMMARY',
        ),
        'nujij'=>array(
            'title'=>'NUjij (Dutch)',
            'share'=>'Submit this to %s',
            'url'=>'http://nujij.nl/jij.lynkx?t=TITLE&amp;u=ORIG_URL&amp;b=SUMMARY',
        ),
        'ekudos'=>array(
            'title'=>'eKudos (Dutch)',
            'share'=>'Submit this to %s',
            'url'=>'http://www.ekudos.nl/artikel/nieuw?url=ORIG_URL&amp;title=TITLE&amp;desc=SUMMARY',
        ),
        'netvouz'=>array(
            'title'=>'Netvouz',
            'share'=>'Submit this to %s',
            'url'=>'http://www.netvouz.com/action/submitBookmark?url=ORIG_URL&amp;title=TITLE&amp;popup=no',
        ),
        'netvibes'=>array(
            'title'=>'Netvibes',
            'share'=>'Submit this to %s',
            'url'=>'http://www.netvibes.com/share?title=TITLE&amp;url=ORIG_URL',
        ),
        'fleck'=>array(
            'title'=>'Fleck',
            'share'=>'Share this on %s',
            'url'=>'http://beta3.fleck.com/bookmarklet.php?url=ORIG_URL&amp;title=TITLE',
        ),
        'blogospherenews'=>array(
            'title'=>'Blogosphere News',
            'share'=>'Share this on %s',
            'url'=>'http://www.blogospherenews.com/submit.php?url=ORIG_URL&amp;title=TITLE',
        ),
        'webblend'=>array(
            'title'=>'Web Blend',
            'share'=>'Blend this!',
            'url'=>'http://thewebblend.com/submit?url=ORIG_URL&amp;title=TITLE&amp;body=SUMMARY',
        ),
        'wykop'=>array(
            'title'=>'Wykop (Polish)',
            'share'=>'Add this to %s',
            'url'=>'http://www.wykop.pl/dodaj?url=ORIG_URL&amp;title=TITLE',
        ),
        'blogengage'=>array(
            'title'=>'BlogEngage',
            'share'=>'Engage with this article!',
            'url'=>'http://www.blogengage.com/submit.php?url=ORIG_URL',
        ),
        'hyves'=>array(
            'title'=>'Hyves',
            'share'=>'Share this on %s',
            'url'=>'http://www.hyves.nl/profilemanage/add/tips/?name=TITLE&amp;text=SUMMARY+-+ORIG_URL&amp;rating=5',
        ),
        'pusha'=>array(
            'title'=>'Pusha (Swedish)',
            'share'=>'Push this on %s',
            'url'=>'http://www.pusha.se/posta?url=ORIG_URL&amp;title=TITLE',
        ),
        'hatena'=>array(
            'title'=>'Hatena Bookmarks (Japanese)',
            'share'=>'Bookmarks this on %s',
            'url'=>'http://b.hatena.ne.jp/add?mode=confirm&amp;url=ORIG_URL&amp;title=TITLE',
        ),
        'mylinkvault'=>array(
            'title'=>'MyLinkVault',
            'share'=>'Store this link on %s',
            'url'=>'http://www.mylinkvault.com/link-page.php?u=ORIG_URL&amp;n=TITLE',
        ),
        'slashdot'=>array(
            'title'=>'SlashDot',
            'share'=>'Submit this to %s',
            'url'=>'http://slashdot.org/bookmark.pl?url=ORIG_URL&amp;title=TITLE',
        ),
        'squidoo'=>array(
            'title'=>'Squidoo',
            'share'=>'Add to a lense on %s',
            'url'=>'http://www.squidoo.com/lensmaster/bookmark?ORIG_URL',
        ),
        'propeller'=>array(
            'title'=>'Propeller',
            'share'=>'Submit this story to %s',
            'url'=>'http://www.propeller.com/submit/?url=ORIG_URL',
        ),
        'faqpal'=>array(
            'title'=>'FAQpal',
            'share'=>'Submit this to %s',
            'url'=>'http://www.faqpal.com/submit?url=ORIG_URL',
        ),
        'evernote'=>array(
            'title'=>'Evernote',
            'share'=>'Clip this to %s',
            'url'=>'http://www.evernote.com/clip.action?url=ORIG_URL&amp;title=TITLE',
        ),
        'meneame'=>array(
            'title'=>'Meneame (Spanish)',
            'share'=>'Submit this to %s',
            'url'=>'http://meneame.net/submit.php?url=ORIG_URL',
        ),
        'bitacoras'=>array(
            'title'=>'Bitacoras (Spanish)',
            'share'=>'Submit this to %s',
            'url'=>'http://bitacoras.com/anotaciones/ORIG_URL',
        ),
        'jumptags'=>array(
            'title'=>'JumpTags',
            'share'=>'Submit this link to %s',
            'url'=>'http://www.jumptags.com/add/?url=ORIG_URL&amp;title=TITLE',
        ),
        'bebo'=>array(
            'title'=>'Bebo',
            'share'=>'Share this on %s',
            'url'=>'http://www.bebo.com/c/share?Url=ORIG_URL&amp;Title=TITLE',
        ),
        'n4g'=>array(
            'title'=>'N4G',
            'share'=>'Submit tip to %s',
            'url'=>'http://www.n4g.com/tips.aspx?url=ORIG_URL&amp;title=TITLE',
        ),
        'strands'=>array(
            'title'=>'Strands',
            'share'=>'Submit this to %s',
            'url'=>'http://www.strands.com/tools/share/webpage?title=TITLE&amp;url=ORIG_URL',
        ),
        'orkut'=>array(
            'title'=>'Orkut',
            'share'=>'Promote this on %s',
            'url'=>'http://promote.orkut.com/preview?nt=orkut.com&amp;tt=TITLE&amp;du=ORIG_URL&amp;cn=SUMMARY',
        ),
        'tumblr'=>array(
            'title'=>'Tumblr',
            'share'=>'Share this on %s',
            'url'=>'http://www.tumblr.com/share?v=3&amp;u=ORIG_URL&amp;t=TITLE',
        ),
        'stumpedia'=>array(
            'title'=>'Stumpedia',
            'share'=>'Add this to %s',
            'url'=>'http://www.stumpedia.com/submit?url=ORIG_URL&amp;title=TITLE',
        ),
        'current'=>array(
            'title'=>'Current',
            'share'=>'Post this to %s',
            'url'=>'http://current.com/clipper.htm?url=ORIG_URL&amp;title=TITLE',
        ),
        'blogger'=>array(
            'title'=>'Blogger',
            'share'=>'Blog this on %s',
            'url'=>'http://www.blogger.com/blog_this.pyra?t&amp;u=ORIG_URL&amp;n=TITLE&amp;pli=1',
        ),
        'plurk'=>array(
            'title'=>'Plurk',
            'share'=>'Share this on %s',
            'url'=>'http://www.plurk.com/m?content=TITLE+-+ORIG_URL&amp;qualifier=shares',
        ),
        
        'google'=>array(
            'title'=>'Google Bookmarks',
            'share'=>'Add this to %s',
            'url'=>'http://www.google.com/bookmarks/mark?op=add&amp;bkmk=ORIG_URL&amp;title=TITLE',
        ),
        
        'googlebuzz'=>array(
            'title'=>'Google Buzz',
            'share'=>'Post this to %s',
            'url'=>'http://www.google.com/buzz/post?url=ORIG_URL',
        ),
        
        'googleplusone'=>array(
            'title'=>'Google +1',
            'share'=>'Submit this to %s',            
            //'html' => '<g:plusone size="small"></g:plusone>',
            'html' => '<div id="plus-one-button-content-AUTO_INCREMENT"><script type="text/javascript"> 
                gapi.plusone.render("plus-one-button-content-AUTO_INCREMENT",
                {"size"    : "small",
                "count"   : true,
                "lang"    : "en",
                "href"    : "ORIG_URL",
                "callback": "plusOnedPublicly"});
                </script></div>',
            'js' => 'https://apis.google.com/js/plusone.js',
        ),
    );

    public function getNames()
    {
        return array_keys($this->_bookmarks);
    }

    public function getBookmarks()
    {
        return $this->_bookmarks;
    }

    public function getBookmark($name)
    {
        return $this->_bookmarks[$name];
    }

    public function getBookmarkTitle($name)
    {
        return $this->__($this->_bookmarks[$name]['title']);
    }

    public function getBookmarkShare($name)
    {
        return $this->__($this->_bookmarks[$name]['share'], $this->_bookmarks[$name]['title']);
    }

    public function getBookmarkUrl($name)
    {
        if (!$this->_origUrl){
            $this->_origUrl = Mage::helper('core/url')->getCurrentUrl();
            $shortener = Mage::getModel('socialbooster/url');
            $this->_shortUrl = urlencode($shortener->filter($this->_origUrl));
            $this->_origUrl = urlencode($this->_origUrl);
        }
        if (!$this->_title){
            if ($headBlock = Mage::app()->getLayout()->getBlock('head')){
                $this->_title = $headBlock->getTitle();
            }
            if (strlen($this->_title) >= 80) {
                $this->_shortTitle = urlencode(substr($this->_title, 0, 80)."[..]");
            }
            else {
                $this->_shortTitle = urlencode($this->_title);
            }
            $this->_title = urlencode($this->_title);
        }
        if (!$this->_summary){
            if ($product = Mage::registry('current_product')){
                $this->_summary = $product->getShortDescription();
            }

            $this->_summary = urlencode(substr(strip_tags($this->_summary),0,300));
            $this->_summary = str_replace('+','%20',$this->_summary);
            $this->_summary = str_replace("&#8217;","'",$this->_summary);
            $this->_summary = stripslashes($this->_summary);
        }
        if (!$this->_siteName){
            $this->_siteName = Mage::app()->getStore()->getWebsite()->getName();
        }

        $url = $this->_bookmarks[$name]['url'];
        $url = str_replace(
            array('ORIG_URL', 'SHORT_URL', 'SHORT_TITLE', 'TITLE', 'SUMMARY', 'SITE_NAME'),
            array($this->_origUrl, $this->_shortUrl, $this->_shortTitle, $this->_title, $this->_summary, $this->_siteName),
            $url
        );
        return $url;
    }
    
    public function getBookmarkHTML($name)
    {                
        if (isset($this->_bookmarks[$name]['html'])) {
            if (!$this->_origUrl){
                $this->_origUrl = Mage::helper('core/url')->getCurrentUrl();
                $shortener = Mage::getModel('socialbooster/url');
                $this->_shortUrl = urlencode($shortener->filter($this->_origUrl));
                $this->_origUrl = urlencode($this->_origUrl);
            }                                                    
            return str_replace(array('ORIG_URL', 'AUTO_INCREMENT'), array($this->getOrigUrl(), $this->getAutoIncrement()), $this->_bookmarks[$name]['html']);
        } else {
            return false;            
        }
    }
    
    public function addBookmarkJs($name)
    {                
        if (isset($this->_bookmarks[$name]['js'])) {
            $block = '<script type="text/javascript" src="'.$this->_bookmarks[$name]['js'].'"></script>';
            $this->_bookmarks[$name]['js'] = null;
            return $block;
        }
        return false;
    }
    
    public function getOrigUrl()
    {                
        if (!$this->_origUrl){
            $this->_origUrl = Mage::helper('core/url')->getCurrentUrl();
            $shortener = Mage::getModel('socialbooster/url');
            $this->_shortUrl = urlencode($shortener->filter($this->_origUrl));
            $this->_origUrl = urlencode($this->_origUrl);
        }
        return urldecode($this->_origUrl);
    }
    
    public function getAutoIncrement()
    {                
        $this->_autoIncrement++;
        return $this->_autoIncrement;                
    }
    
    
    
}