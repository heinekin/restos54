<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<blocs>
    <bloc type="script">
        <content><![CDATA[ MyLoader.load('ProfileGrid','ad54'); ]]></content>
    </bloc>
    <bloc type="page">
        <content method="innerHTML" id="main"><!--
            <h1>Configuration des profils</h1>
            <table id="tableMain">
                <tr>
                    <td valign="top" id="leftMain" width="320">
            <table cellspacing="0" border="1" id="tableProfile" width="300">
                <thead>
                    <tr style="background:#eeeeee;">
                        <th sortable="true" resizable="true" width="30">Id</th>
                        <th sortable="true" resizable="true" width="60">Code</th>
                        <th sortable="false" fixed="true" width="75">Supprimer</th>
                        <th resizable="false" fixed="true" width="50">Droits</th>
                    </tr>
                </thead>
                <tbody>
                {section name=idx loop=$dataProfile}
                    <tr>
                        <td>{$dataProfile[idx].id}</td>
                        <td><span id="spanProfile_{$dataProfile[idx].id}" onclick="ProfileGrid.editCode($(this));" style="display: block;">{$dataProfile[idx].code}</span></td>
                        <td><a class="lien" onclick="MyDataGrid.action('remove', {literal}{{/literal}id: {$dataProfile[idx].id}{literal}}{/literal});" title="Supprimer"><img src="/images/icon/delete.png" alt="Supprimer"/></a></td>
                        <td><a class="lien" onclick="MyDataGrid.action('right', {literal}{{/literal}id: {$dataProfile[idx].id}{literal}}{/literal});" title="Droits"><img src="/images/icon/key.png" alt="Droits"/></a></td>
                    </tr>
                {/section}
                </tbody>
            </table>
                    </td>
                    <td valign="top"><div id="rightMain" />
                    </td>
                </tr>
            </table>
        --></content>
    </bloc>
    <bloc type="script">
        <content><![CDATA[
            {literal}
            var config = {
                tableId: 'tableProfile',
                config: {
                    height: 250,
                    width: 220,
                    stripeRows: true
                },
                resize: {
                    active: true
                },
                functions: {
                    remove: {
                        action: '/profile/delete/',
                        method: 'post',
                        confirm: {
                            title: 'Confirmation',
                            msg: 'Etes-vous sur de vouloir supprimer ce profil ?'
                        }
                    },
                    add: {
                        action: '/profile/add/',
                        method: 'get'
                    },
                    right: {
                        action: '/profile/right/',
                        method: 'get'
                    }
                },
                buttons: [
                    {
                        value: 'Ajouter',
                        action: "MyDataGrid.action('add');",
                        className: 'small_btn'
                    }
                ]
            };
            MyDataGrid.render(config);
            {/literal}
        ]]></content>
    </bloc>
    <bloc type="page">
        <content method="innerHTML" id="bottomProfileList"><![CDATA[
            <input type="button" onclick="ProfileGrid.add();" value="Ajouter" class="small_btn" />
        ]]></content>
    </bloc>
</blocs>