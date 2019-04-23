<?php
    require_once('class/Post.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $message = new Post($_POST['name'], $_POST['surname'], $_POST['email'], $_POST['comment'], $_FILES['image']);
        //validating the data from form
        $errors = $message->validate();
        //if no errors, proceed with saving data to DB and mailing it
        if (empty($errors)) {
            $message->save();
            $message->_mail();
            //preventing resubmitting the form with page refresh
            echo "<script>
                    if ( window.history.replaceState ) {
                        window.history.replaceState( null, null, window.location.href );
                    }
                </script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spheremall Task</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                <form method="POST" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" placeholder="Your name" class="form-control">
                        <?php if (!empty($errors['name'])) { echo "<div>" . $errors['name'] . "</div>"; } ?>
                    </div>
                    <div class="form-group">
                        <label for="surname">Surname</label>
                        <input type="text" name="surname" placeholder="Your surname" class="form-control">
                        <?php if (!empty($errors['surname'])) { echo "<div>" . $errors['surname'] . "</div>"; } ?>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" placeholder="Your email address" class="form-control">
                        <?php if (!empty($errors['email'])) { echo "<div>" . $errors['email'] . "</div>"; } ?>
                    </div>
                    <div class="form-group">
                        <label for="comment"></label>
                        <textarea name="comment" cols="30" rows="10" placeholder="Your comment" class="form-control"></textarea>
                        <?php if (!empty($errors['message'])) { echo "<div>" . $errors['message'] . "</div>"; } ?>
                    </div>
                    <div class="form-group">
                        <label for="image"></label>
                        <input type="file" name="image" class="form-control">
                        <?php if (!empty($errors['image'])) { echo "<div>" . $errors['image'] . "</div>"; } ?>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                </form>
            </div>
        </div>
    </div>
  </body>
</html>