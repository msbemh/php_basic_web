<!DOCTYPE html>
<html lang="en">

<head>
    <?php require '../head.php'; ?>
    <script>

        $(document).on('ready', function () {
            
        });

        function is_empty(str) {
            return (str == '' || str == undefined || str == null || str == 'null');
        }

    </script>
</head>

<body>
    <?php require '../header.php'; ?>

    <div id="vue_app"></div>

    <script src="../dist/vue_bundle.js"></script>

    <?php require '../footer.php'; ?>

    <?php require '../copyright.php'; ?>
</body>

</html>