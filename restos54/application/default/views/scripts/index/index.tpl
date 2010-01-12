<html>
<head>
    <title>Restos du Coeur - Système d'information</title>
    <meta http-equiv="Content-Type" content="text /html; charset=UTF-8" />
    {include file='index/include/headerScript.tpl'}

</head>
<body>

    <div>
  		<div id="bohd_logo_top">
  			<img height="43" alt="Rdc_logo" src="images/rdc_header.jpg"/><div id="menu_theme" style="float:right;width:275"></div>
  		</div>
                
        <div id="bohd_container">
            <table class="mainTbl">
                <tr>
                    <td class="leftMainTbl"><div id="menu" /></td>

                    <td class="spaceTd"></td>
                    <td valign="top">
                        <table width="100%" height="100%">
                            <tr>
                                <td class="topMainTbl">
                                    <div>
                                        <div class="topMainDivLeft">
                                            {include file='index/include/userConnected.tpl'}
                                        </div>
                                        <div class="topMainDivRight">
                                            <b>{$camp}</b>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="spaceTr"></td>
                            </tr>

                            <tr>
                                <td class="navMainTbl" id="navbar"></td>
                            </tr>
                            <tr>
                                <td id="main" class="centerMainTbl"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
  	</div>
</body>
</html>
