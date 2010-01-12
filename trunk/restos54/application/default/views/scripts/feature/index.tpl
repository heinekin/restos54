<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="script">
        <content><![CDATA[ MyLoader.load('FeatureList','ad54'); ]]></content>
    </bloc>
    <bloc type="page">
        <content method="innerHTML" id="main"><![CDATA[
        <h1>Fonctionnalités (Attention ! A modifier avec précaution)</h1>
        <table id="tableMain">
            <tr>
                <td valign="top" id="leftMain" width="400"></td>
                <td valign="top"><div id="rightMain" /></td>
            </tr>
        </table>
        ]]></content>
    </bloc>
    <bloc type="script">
        <content><![CDATA[
            window.setTimeout("FeatureList.init()", 500);
            window.setTimeout("FeatureList.render('leftMain')", 500);
        ]]></content>
    </bloc>
</blocs>