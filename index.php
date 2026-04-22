<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager with Download1</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 30px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header-flex { display: flex; justify-content: space-between; align-items: center; }
        .file-card { background: #fff; border: 1px solid #ddd; margin-bottom: 10px; padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; }
        .btn { padding: 8px 15px; cursor: pointer; border: none; border-radius: 5px; font-size: 13px; text-decoration: none; display: inline-block; }
        .btn-upd { background: #007bff; color: white; }
        .btn-del { background: #dc3545; color: white; }
        .btn-down { background: #28a745; color: white; }
        .btn-all { background: #6c757d; color: white; margin-bottom: 20px; }
        .upload-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 2px dashed #ccc; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-flex">
        <h2>📁 File Manager</h2>
        <a href="api.php?action=download_all" class="btn btn-all">📥 Download All (ZIP)</a>
    </div>
    
    <div class="upload-box">
        <input type="file" id="fileInput">
        <button class="btn btn-upd" onclick="uploadFile()">Upload New File</button>
    </div>

    <div id="fileList">
        </div>
</div>

<script>
    $(document).ready(loadFiles);

    function loadFiles() {
        $.getJSON('api.php?action=list', function(files) {
            $('#fileList').html('');
            if(files.length === 0) {
                $('#fileList').html('<p style="text-align:center; color: #888;">Koi file nahi mili.</p>');
                return;
            }
            files.forEach(file => {
                let card = `
                    <div class="file-card">
                        <div>
                            <strong>${file}</strong>
                        </div>
                        <div>
                            <a href="api.php?action=download&filename=${file}" class="btn btn-down">Download</a>
                            <button class="btn btn-upd" onclick="updateFile('${file}')">Update</button>
                            <button class="btn btn-del" onclick="deleteFile('${file}')">Delete</button>
                        </div>
                    </div>`;
                $('#fileList').append(card);
            });
        });
    }

    function uploadFile() {
        let file_data = $('#fileInput').prop('files')[0];
        if(!file_data) return alert("Pehle file select karein!");

        let form_data = new FormData();
        form_data.append('file', file_data);

        $.ajax({
            url: 'api.php?action=upload',
            type: 'POST',
            processData: false,
            contentType: false,
            data: form_data,
            success: function() {
                alert("File upload ho gayi!");
                $('#fileInput').val('');
                loadFiles();
            }
        });
    }

    function updateFile(filename) {
        alert("'" + filename + "' ko update karne ke liye nayi file select karein aur upload karein (same name se overwrite ho jayegi).");
        $('#fileInput').click();
    }

    function deleteFile(filename) {
        if(confirm("Kya aap '" + filename + "' ko delete karna chahte hain?")) {
            $.post('api.php?action=delete', {filename: filename}, function() {
                loadFiles();
            });
        }
    }
</script>

</body>
</html>
