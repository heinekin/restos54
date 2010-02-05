<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="script">
        <content><![CDATA[ MyLoader.load('FeatureRight','ad54'); ]]></content>
    </bloc>
   <bloc type="page">
        <content method="innerHTML" id="rightMain"><![CDATA[
            <h2>Modifier les droits d'accès aux fonctionnalités</h2>
            <div id="featureTree"></div>
        ]]></content>
    </bloc>
    <bloc type="script">
        <content><![CDATA[
            window.setTimeout("FeatureRight.init({$profileId})", 500);
            window.setTimeout("FeatureRight.render('featureTree')", 500);
        ]]></content>
    </bloc>

</blocs>