<!DOCTYPE html>
<html>

<head>
    <title>home page</title>

    <style>
        #btnnav:hover{
  background-color:green;
  color:white;
}

#conpage:hover{
    background-color:green;
  color:white;
}

    </style>


    <link rel="stylesheet" type="text/css" href="style.css">

    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">



</head>

<body id="demo">
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark p-4 ">
        <div class="container-fluid">
            <a class="navbar-brand" href="javascript:void(0)">OES</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mynavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">Contact</a>
                    </li>
                </ul>
                <form class="d-flex">
                <a href="teacher-login.php"  class="btn btn-primary" type="button" style="margin-right:50px;" id="btnnav">Login Teacher</a>
          <a href="student-login.php"  class="btn btn-primary" type="button" style="margin-right:50px;" id="btnnav">Login Student</a>
                </form>
            </div>
        </div>
    </nav>



    <!--contact section-->

    <div class="container mt-3">
  <form action="/action_page.php">
    <div class="mb-3 mt-3">
    <h1 style="text-align: center; color:white;  font-weight: bolder;text-shadow: 
    2px 2px 4px rgba(255, 0, 0, 0.7),
    -2px -2px 4px rgba(0, 255, 0, 0.7),
    2px -2px 4px rgba(0, 0, 255, 0.7),
    -2px 2px 4px rgba(255, 255, 0, 0.7); padding-bottom:50px; ">contact</h1>
      <label for="email">Full name</label>
      <input type="email" class="form-control" id="name" placeholder="Enter name" name="name">
    </div>
    <div class="mb-3 mt-3">
      <label for="email">Email:</label>
      <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
    </div>
    <div class="mb-3">
      <label for="pwd">Subject</label>
      <input type="password" class="form-control" id="subject" placeholder="subject" name="subject">
    </div>
    <div class="mb-3">
      <label for="pwd">Phone number</label>
      <input type="password" class="form-control" id="phone" placeholder="Enter phone" name="phone">
    </div>

    <div class="mb-3 mt-3">
      <label for="comment">Comments:</label>
      <textarea class="form-control" rows="5" id="comment" name="text"></textarea>
    </div>
    <div class="form-check mb-3">
      <label class="form-check-label">
        <input class="form-check-input" type="checkbox" name="remember"> Remember me
      </label>
    </div>
    <button type="submit" class="btn btn-primary" id="btnnav">Send message</button>
  </form>
</div>




    <div class="container-fluid mt-3">

        <div class="row">
      

            <div class="col p-6">
                <div class=" bg-primary text-white h-100 p-4">
                    <h2>By Phone</h2>
                    <p>9786575576</p>
                    <p>9876459623</p>
                    <p>9834238783</p>
                </div>
            </div>
            <div class="col p-6 " >
                <div class=" bg-dark text-white  h-100 p-4" id="conpage" >
                    <h2>By email</h2>
                    <p>office@gmail.com</p>
                    <p>abc@gmail.com</p>
                </div>
            </div>
        </div>
    </div>

    <div class=" .container-fluid">
        <div class="footer bg-dark">
            <div class="row">
                <div class="col">
                    <div>
                        <h4>online examaition system</h4>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <h4>usefull links</h4>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">about</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="">contact</a>
                        </li>
                    </div>
                </div>
                <div class="col">
                    <h4>address</h4>
                    <p>Bagnaha,3 Thakurbaba Bardiya Nepal </p>

                    <p>office@gmail.com</p>
                    <p>01-2356789(Reception)<br> 01-9874653(school gate),<br>01873433232(account section) <br>9807826583(office)</p>
                </div>
            </div>

        </div>


    </div>


    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>