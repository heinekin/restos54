var MyRest = {

    config: null,
    app: null,
    proxy: null,
    reader: null,
    writer: null,
    store: null,
    userColumns: [],
    editor: null,
    grid: null,
    window: null,

    init: function(config)
    {
        this.config = config;
        this.initApp();
        this.initProxy();
        this.initRW();
        this.initStore();
        this.initColumns();
        this.store.load();
        this.initEditor();
        this.initGrid();

    },

    initApp: function()
    {
        this.app = new Ext.App({});
    },

    initProxy: function()
    {
        // Create a standard HttpProxy instance.
        this.proxy = new Ext.data.HttpProxy({url: this.config.proxy});
    },

    initRW: function()
    {
        // Typical JsonReader.  Notice additional meta-data params for defining the core attributes of your json-response
        this.reader = new Ext.data.JsonReader({
            totalProperty: "total",
            successProperty: "success",
            idProperty: "id",
            root: "data"
        }, this.config.reader
        );

        // The new DataWriter component.
        this.writer = new Ext.data.JsonWriter(this.config.writer);
    },

    initStore: function()
    {
        // Typical Store collecting the Proxy, Reader and Writer together.
        this.store = new Ext.data.Store({
            id: this.config.idStore,
            restful: true,     // <-- This Store is RESTful
            proxy: this.proxy,
            reader: this.reader,
            writer: this.writer,    // <-- plug a DataWriter into the store just as you would a Reader
            listeners: {
                write : this.config.listeners
            }
        });
    },

    initColumns: function()
    {
        // Let"s pretend we rendered our grid-columns with meta-data from our ORM framework.
        this.userColumns =  this.config.columns;
    },

    initEditor: function()
    {
        // use RowEditor for editing
        this.editor = new Ext.ux.grid.RowEditor(this.config.editor);
    },

    initGrid: function()
    {
        this.grid = new Ext.grid.GridPanel(this.config.grid);
    },

    /**
     * onAdd
     */
    onAdd: function(btn, ev) {
        var u = new this.store.recordType({
            first : "",
            last: "",
            email : ""
        });
        this.editor.stopEditing();
        this.grid.store.insert(0, u);
        this.editor.startEditing(0);
    },
    /**
     * onDelete
     */
     onDelete: function() {
        var rec = this.grid.getSelectionModel().getSelected();
        if (!rec) {
            return false;
        }
        this.grid.store.remove(rec);
    }


};

