<html>
    <head>
        <title>checkboxtree</title>
        <link rel="stylesheet" type="text/css" href="/js/ext/resources/css/ext-all.css" />
        <link rel="stylesheet" type="text/css" href="/css/main.css" />
        <script type="text/javascript" src="/js/ext/adapter/prototype/prototype.js"></script>
        <script type="text/javascript" src="/js/ext/adapter/prototype/scriptaculous.js"></script>
        <script type="text/javascript" src="/js/ext/adapter/prototype/ext-prototype-adapter.js"></script>
        <script type="text/javascript" src="/js/ext/ext-all-debug2.js"></script>
        <script type="text/javascript" src="/js/class/checkboxtree.js"></script>
        {literal}
        <style>
            .label_checked {
                font-style: italic;
            }
            .label_checked span {
                 background-color: #EEF;
            }
        </style>
        {/literal}
    </head>
    <body>
        <div id="main"></div>
        <!--<table border="0">
            <tr>
                <td><div id="main"></div></td>
                <td valign="top">
                    <div id="debug" style="background-color: #FFF; width: 400px; margin: 20px; overflow: auto; height: 500px;"></div>
                </td>
            </tr>
        </table>-->

        <script>
            window.setTimeout('FeatureList.init({$json})', 100);
            window.setTimeout("FeatureList.render('main')", 200);
            window.setTimeout("FeatureList.initChecked()", 1000);
        </script>

    </body>
</html>