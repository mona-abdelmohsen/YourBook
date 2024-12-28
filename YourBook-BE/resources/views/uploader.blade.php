<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .my-config {
            --darkmode: 0;
            --h-accent: 223;
            --s-accent: 100%;
            --l-accent: 61%;
        }
    </style>
</head>
<body>
<lr-config
    ctx-name="my-uploader"
    pubkey="f3fd6eb40036c3ec33cf"
    max-local-file-size-bytes="10000000"
    source-list="local, url, camera, dropbox, facebook, gdrive, gphotos, instagram"
></lr-config>

<lr-file-uploader-regular
    css-src="https://cdn.jsdelivr.net/npm/@uploadcare/blocks@0.30.5/web/lr-file-uploader-regular.min.css"
    ctx-name="my-uploader"
    class="my-config"
>
</lr-file-uploader-regular>

<lr-upload-ctx-provider
    ctx-name="my-uploader"
></lr-upload-ctx-provider>


<script type="module">
    import * as LR from "https://cdn.jsdelivr.net/npm/@uploadcare/blocks@0.30.5/web/lr-file-uploader-regular.min.js";

    LR.registerBlocks(LR);


    const dataOutput = document.querySelector('lr-upload-ctx-provider');
    dataOutput.addEventListener('data-output', (e) => {
        console.log('Data Output: ', e.detail);
    });
    dataOutput.addEventListener('upload-finish', (e) => {
        console.log('Upload Finish: ',e.detail);
    });

</script>


</body>
</html>
