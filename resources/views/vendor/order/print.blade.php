<html>
    <head>
    <style type="text/css">
        @page {
            size: auto;
            margin: 0mm;
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
                height: 99%;

            }

            /*  html, body {
    width: 210mm;
    height: 287mm;
  }
 */
            html {}

            ::-webkit-scrollbar {
                width: 0px;
                /* remove scrollbar space */
                background: transparent;
                /* optional: just make scrollbar invisible */
            }
    </style>
</head>

<body>
@php
$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();

@endphp




<table style="border:none" width="100%">
    <tbody>
        <tr>
            <td><img width="200px"
src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('355359371107153', $generatorPNG::TYPE_CODE_128)) }}"></td>

</tr>
<tr>
            <td><img width="200px"
src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('355359371107333', $generatorPNG::TYPE_CODE_128)) }}"></td>

</tr>
<tr>
            <td><img width="200px"
src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('355359371107123', $generatorPNG::TYPE_CODE_128)) }}"></td>

</tr>
<tr>
            <td><img width="200px"
src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('355359371107433', $generatorPNG::TYPE_CODE_128)) }}"></td>

</tr>
<tr>
            <td><img width="200px"
src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('355359371107123', $generatorPNG::TYPE_CODE_128)) }}"></td>

</tr>
<tr>
            <td><img width="200px"
src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('355359371107183', $generatorPNG::TYPE_CODE_128)) }}"></td>

</tr>
<tr>
            <td><img width="200px"
src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('355359371107132', $generatorPNG::TYPE_CODE_128)) }}"></td>

</tr>
<tr>
            <td><img width="200px"
src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('355359371107112', $generatorPNG::TYPE_CODE_128)) }}"></td>

</tr>
<tr>
            <td><img width="200px"
src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('355359371107122', $generatorPNG::TYPE_CODE_128)) }}"></td>

</tr>
<tr>
            <td><img width="200px"
src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('355359371107142', $generatorPNG::TYPE_CODE_128)) }}"></td>

</tr>
<tr>
            <td><img width="200px"
src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('355359371107132', $generatorPNG::TYPE_CODE_128)) }}"></td>

</tr>

    </tbody>

</table>


</body>

</html>


