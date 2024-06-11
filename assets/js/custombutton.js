tinymce.PluginManager.add(
  "custombutton",
  tinymce.PluginManager.createPlugin(
    "custombutton",
    function (editor, settings) {
      editor.addButton("custombutton", {
        text: "My Custom Button",
        icon: "fa fa-star", // Replace with your desired icon class (e.g., from Font Awesome)
        onclick: function () {
          // Add your custom button functionality here
          // For example, insert text, open a dialog, etc.
          editor.insertText("[Custom Button Clicked]");
        },
      });
    }
  )
);
