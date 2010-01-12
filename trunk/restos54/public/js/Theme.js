Ext.ux.ThemeCombo = Ext.extend(Ext.form.ComboBox, {
	// configurables
	themeBlueText: 'Bleu'
	,themeGrayText: 'Gris'
	,themeDarkGrayText: 'Gris foncé'
	,themeSlateText: 'Ardoise'
	,themeGreenText: 'Vert'
	,themeMidnightText: 'Minuit'
	,themeIndigoText: 'Indigo'
	,themeBlackText: 'Noir'
        ,themeOliveText: 'Olive'
        ,themePurpleText: 'Violet'
        ,themeUbuntuText: 'Ubuntu'
	,themeVar:'theme'
	,selectThemeText: 'Changer thème'
	,lazyRender:true
	,lazyInit:true
	,cssPath:'js/ext/resources/css/'
	,initComponent:function(){
		Ext.apply(this,{
			store: new Ext.data.SimpleStore({
				fields: ['themeFile', {name:'themeName', type:'string'}]
				,data: [
				['xtheme-default.css', this.themeBlueText]
				,['xtheme-slate.css', this.themeSlateText]
				,['xtheme-green.css', this.themeGreenText]
				,['xtheme-midnight.css', this.themeMidnightText]
				,['xtheme-indigo.css', this.themeIndigoText]
				,['xtheme-gray-extend.css', this.themeGrayText]
                                ,['xtheme-purple.css', this.themePurpleText]
                                ,['xtheme-olive.css', this.themeOliveText]
				,['xtheme-darkgray.css', this.themeDarkGrayText]
				,['xtheme-black.css', this.themeBlackText]
				,['xtheme-human.css', this.themeUbuntuText]
				]
			})
			,valueField: 'themeFile'
			,displayField: 'themeName'
			,triggerAction:'all'
			,mode: 'local'
			,forceSelection:true
			,editable:false
			,fieldLabel: this.selectThemeText
		});
		// end of apply  
		this.store.sort('themeName');
		// call parent  
		Ext.ux.ThemeCombo.superclass.initComponent.apply(this, arguments);
		if(false !== this.stateful && Ext.state.Manager.getProvider()) {
			this.setValue(Ext.state.Manager.get(this.themeVar) || 'Bleu');
		}
		else {
			this.setValue('xtheme-default.css');
		}
	}
	,setValue:function(val) {
		Ext.ux.ThemeCombo.superclass.setValue.apply(this, arguments);
		// set theme 
		Ext.util.CSS.swapStyleSheet(this.themeVar, this.cssPath + val);
		if(false !== this.stateful && Ext.state.Manager.getProvider()) {
			Ext.state.Manager.set(this.themeVar, val);
		}
}
});
Ext.reg('themecombo', Ext.ux.ThemeCombo);