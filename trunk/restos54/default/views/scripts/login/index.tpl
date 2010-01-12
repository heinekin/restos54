{$this->doctype()}
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Restos du Coeur</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" media="screen" href="/css/main.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="/css/auth.css" />
    </head>
    <body>
	    <div id="content">
	    	<div class="authFormContainer">
                <div class="authLogo"><img alt="Logo" src="/images/rdc_header.jpg" /></div>
                <div id="authForm">
                    <div class="authFormContent">
                    {$formulaire}
                    </div>
                    <div id="render_auth" class="centerContent">
                        <span class="error_list">{if $message[0] != ''}{$message[0]}{else}&nbsp;{/if}</span>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
