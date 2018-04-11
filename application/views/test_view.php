<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url('favicon.ico');?>" />
    <title>Codeigniter Web app | PDF to JSON with preview</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('/assets/js/bootstrap.js');?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('/assets/js/bootstrap.min.js');?>" />

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,100,500' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js"></script>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('/assets/css/bootstrap.min.css');?>" />
    <!-- Custom styles for this template -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('/assets/css/blog-post.css');?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('/assets/css/style.css');?>" />

</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">Web App | PDF to JSON</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">

        </div>
    </div>
</nav>

<div class="container">
    <div class="row">

        <div class="col-md-8">
                <p class="lead">
                <img src="<?php echo base_url('img/bens-logo.png');?>" height="60" width="200" title="Bens Consulting" class="img-icon-service">
            </p>
            <hr>
            <p><?php echo isset($description) ? $description : '' ?></p>
            <hr>
            <div class="container" style="background: #f5f5f5; margin-top:5%;">
                <div class="row">
                    <div class="main-connection-div" id="main-connection-div">
                        <p class="title-log-in-main-connection-div">Upload | Preview | Convert</p>
                        <form method="post" id="upload_pdf">
                            <div class="btn-group" role="group">
                            <div class="input-field col-md-4">
                                <input type="text" id="filename" placeholder="Uploaded filename">
                                <label for="filename"></label>
                            </div>
                                <div class="col-md-8">
                                    <a onclick="PreviewImage();" id="preview" class="waves-effect waves-light btn-large" style="margin: 20px 0 0 30px;background: #0072ff; display: none">Preview</a>
                                </div>
                            </div>
                            <div class="btn-group" role="group">
                                <a class="waves-effect waves-light btn-large" style="margin: 20px 0 0 30px; background: #0072ff;">
                                    <input type="file" name="pdf" id="pdf" class="inputfile"/>
                                    <label for="pdf">Upload PDF</label>
                                </a>
                                <a id="convert" class="waves-effect waves-light btn-large" style="margin: 20px 0 0 30px; background: #0072ff;">Convert to JSON</a>

                            </div>
                        </form>
                        <p id="showErrors" style="margin:50px;color:red;"></p>
                  </div>
               </div>

            </div>

            <hr>
            <blockquote class="blockquote">
                <pre style="word-wrap: break-word; white-space: pre-line;" class="mb-0" id="showJson"></pre>
                <footer class="blockquote-footer">This will be
                    <cite title="Source Title">Parsed PDF content in JSON</cite>
                </footer>
            </blockquote>
        </div>

        <div class="col-md-4" id="pdfPreview" style="display: none">
            <div class="card my-4" style="width: 450px; height: 600px;  ">
                <h5 class="card-header">Preview PDF File</h5>
                    <iframe id="viewer" frameborder="0" scrolling="no" width="450" height="600"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white"><?php echo isset($footer_text) ? $footer_text : 'Copyright &copy; Srdjan Stojanovic 2018' ?></p>
    </div>
</footer>
<!-- Bootstrap core JavaScript -->
<script src="<?php echo base_url('/assets/js/jquery.min.js');?>"></script>
<script src="<?php echo base_url('/assets/js/bootstrap.bundle.min.js');?>"></script>
</body>
</html>

<script>

    $("#convert").click(function (e) {
        e.preventDefault();
        if($("#pdf").val() == ''){
           $("#showErrors").text('Please upload PDF file');
           return;
        }

        var formData = new FormData($('#upload_pdf')[0]);

        $.ajax({
            url:"<?php echo base_url('welcome/pdf_upload');?>",
            type: 'POST',
            crossDomain: true,
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,
            success:function (data){

                var jsonPretty =  syntaxHighlight(data);
                $("#showJson").html(jsonPretty);

               // $("#showJson").text(data.ParsedResults[0].ParsedText);
            }
        });
    });

    // Preview uploaded PDF file
    function PreviewImage() {
        pdffile = document.getElementById("pdf").files[0];
        pdffile_url = URL.createObjectURL(pdffile);
        $('#viewer').attr('src',pdffile_url);
    }

   // If PDF file is uploaded we take filename and show it on input instead of placeholder
    $('#pdf').change(function() {
        var filename = $(this).val();
        var lastIndex = filename.lastIndexOf("\\");
        if (lastIndex >= 0) {
            filename = filename.substring(lastIndex + 1);
        }
        $('#filename').val(filename);
        $("#preview").css('display','inline-block');
    });
    // If preview button is clicked, we show uploaded PDF on preview.
    $("#preview").click(function (e) {
        e.preventDefault();
        $("#pdfPreview").css('display','block');
    });


    //https://stackoverflow.com/questions/4810841/how-can-i-pretty-print-json-using-javascript/7220510#7220510
    //Syntax highlighting with some regex magic :)
    function syntaxHighlight(json) {
        if (typeof json != 'string') {
            json = JSON.stringify(json, undefined, 2);
        }
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
            var cls = 'number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'key';
                } else {
                    cls = 'string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'boolean';
            } else if (/null/.test(match)) {
                cls = 'null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    }

</script>
