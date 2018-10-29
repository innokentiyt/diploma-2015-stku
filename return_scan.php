<html>
<head>
    <title>Загрузка бланка</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <script src="jquery-1.11.2.min.js"></script>
    <link rel="stylesheet" href="style.css">
    
    <!-- подключение библиотек декодера QR-кода -->
    <script type="text/javascript" src="lazarsoft/grid.js"></script>
    <script type="text/javascript" src="lazarsoft/version.js"></script>
    <script type="text/javascript" src="lazarsoft/detector.js"></script>
    <script type="text/javascript" src="lazarsoft/formatinf.js"></script>
    <script type="text/javascript" src="lazarsoft/errorlevel.js"></script>
    <script type="text/javascript" src="lazarsoft/bitmat.js"></script>
    <script type="text/javascript" src="lazarsoft/datablock.js"></script>
    <script type="text/javascript" src="lazarsoft/bmparser.js"></script>
    <script type="text/javascript" src="lazarsoft/datamask.js"></script>
    <script type="text/javascript" src="lazarsoft/rsdecoder.js"></script>
    <script type="text/javascript" src="lazarsoft/gf256poly.js"></script>
    <script type="text/javascript" src="lazarsoft/gf256.js"></script>
    <script type="text/javascript" src="lazarsoft/decoder.js"></script>
    <script type="text/javascript" src="lazarsoft/qrcode.js"></script>
    <script type="text/javascript" src="lazarsoft/findpat.js"></script>
    <script type="text/javascript" src="lazarsoft/alignpat.js"></script>
    <script type="text/javascript" src="lazarsoft/databr.js"></script>
	
</head>

<body>
<a href="ved.jpg" target="_blank">Пример отсканированой ведомости</a> <a href="return.php">Назад</a>
<h3>Выберите файл с QR-кодом:</h3>
<form>
    <input type="file" id="imgInp" onchange="handleFiles(this.files)" />
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#photo').attr('src', e.target.result);

                    var imgheight = $('#photo').height();
                    var imgwidth = $('#photo').width();
                    var preheight = $('#preview').height();
                    var prewidth = $('#preview').width();

                    if (imgheight >= imgwidth) {
                        $('#photo').css('height', preheight);
                        $('#photo').css('width', '');
                    } else {
                        $('#photo').css('width', prewidth);
                        $('#photo').css('height', '');
                    }

                    $('#photo').css('display', 'block');
                    $('#preview').css('background', 'none');
                };				

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function(){
                readURL(this);
        });

        var encodedText;
        
        function handleFiles(f){
            //var o=[];
            $('input[name="week1"]').val('');
            $('input[name="subject1"]').val('');
            $('input[name="group1"]').val('');
            for(var i=0; i<f.length; i++){
                var reader = new FileReader();
                reader.onload = (function() {
                    return function(e) {
                        qrcode.callback = function() {
                            //alert('Распознано: '+this.result);
                            
                            encodedText = this.result;
                            $('input[type="submit"]').removeAttr('disabled');
                            $("#res-text").html("<small><small>Распознанная информация:</small></small><br />"+this.result);
                        };
                        qrcode.decode(e.target.result);
                        
                    };
                })(f[i]);
                reader.readAsDataURL(f[i]);	
            }
            
            
        }
    </script>
</form>
<div id="preview" class="pre-image"><img id="photo" src="#" alt="your image" /></div>
<span id="res-text"></span>

<form method="post" id="submitForm">
    <input type="submit" value="Отправить" id="submitButton" disabled />
</form>

</body>
</html>