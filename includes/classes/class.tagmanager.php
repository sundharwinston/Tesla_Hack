<?php

class Tagmanager
{
    
    public static function headtracker(){
		if(SEO_TAGMANAGER === true){
			echo '<script type="text/javascript">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,\'script\',\'dataLayer\',\'GTM-MGSTGJC\');</script><script async src="https://www.googletagmanager.com/gtag/js?id=UA-96526991-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag(\'js\', new Date());

  gtag(\'config\', \'UA-96526991-1\');
</script>';
		}
        
    }
    
    
    
    public static function bodytracker(){
		if(SEO_TAGMANAGER === true){
			echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MGSTGJC"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>';
		}
        
    }
    
}


?>