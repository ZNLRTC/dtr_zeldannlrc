<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <!--[if gte mso 9]><xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml><![endif]-->

    <title>NLRC</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--[if !mso]-->
    <meta content="IE=edge" http-equiv="X-UA-Compatible" />
    <!--<![endif]-->
    <style>
        /* RESET STYLES */
        html,
        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            font-size: 16px;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        table {
            border-collapse: collapse !important;
        }

        table.table-content {
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse;
            border: 1px black solid;
        }

        table.table-content td {
            border: 1px black solid;
            padding: 8px;
        }


        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .w-100 {
            width: 100%;
        }

        .bold {
            font-weight: bold;
        }

        .italic {
            font-style: italic;
        }

        .uppercase {
            text-transform: uppercase;
        }
        .lgray{
            color: #D3D3D3;
        }
        .teal{
            color: #00B1B1;
        }
    </style>
</head>

<body>
<table class="mx-auto w-100" style="max-width: 650px;">
    <tr>
        <td class="center" align="center" valign="top">

            <?php if (isset($template['partials']['header'])) echo $template['partials']['header']; ?>

            <?php echo $template['body']; ?>

            <?php if (isset($template['partials']['footer'])) echo $template['partials']['footer']; ?>

            </center>
        </td>
    </tr>
</table>
</body>

</html>