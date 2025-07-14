import mammoth from "mammoth";
tinymce.PluginManager.add('importDocx', function (editor) {
    editor.ui.registry.addButton('importDocxButton', {
        icon: 'upload',
        title: 'Import Docx',
        tooltip: 'Import File Docx',
        onAction: function () {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', '.docx');

            // User choose file
            input.onchange = function () {
                const file = this.files[0];
                if (!file) {
                    return
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    const arrayBuffer = e.target.result

                    mammoth.convertToHtml({ arrayBuffer })
                        .then(function (result) {
                            editor.setContent(result.value)
                        })
                        .catch(function (err) {
                            console.error('Konversi Docx gagal:', err);
                            alert('Gagal mengimpor file. Pastikan format file adalah .docx yang valid.');
                        });
                }
                reader.readAsArrayBuffer(file);
            }
            input.click();
        }
    })

    // Kembalikan metadata plugin
    return {
        getMetadata: function () {
            return {
                name: 'Docx Importer',
            };
        }
    };
})

