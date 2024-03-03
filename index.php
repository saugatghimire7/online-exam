<!DOCTYPE html>
<html>

<head>
  <title>home page</title>
 <style>
 #btnsub:hover { 
  background-color: green;
  color: white;
}
#btnsub{
  width:250px;
}
#btnnav:hover{
  background-color:green;
  color:white;
}

#btnnav{

}
#tecbtn{
margin-left:120px;
}
 </style>
  <link rel="stylesheet" type="text/css" href="style.css">

  <!-- Latest compiled and minified CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">



</head>

<body id="demo">
  <nav class="navbar navbar-expand-sm navbar-dark bg-dark p-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="javascript:void(0)">OES</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mynavbar">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="about.php">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="contact.php">Contact</a>
          </li>
        </ul>
        <form class="d-flex">     
        <a href="teacher-login.php"  class="btn btn-primary" type="button" style="margin-right:50px;" id="btnnav">Login Teacher</a>
          <a href="student-login.php"  class="btn btn-primary" type="button" style="margin-right:50px;" id="btnnav">Login Student</a>
  
        </form>
      </div>
    </div>
  </nav>

  <div class=".container-fluid">
    <div style="height: 100vh; color:black; opacity:80%; position:absolute;"></div>
    <div class="demo" style="color:black;  font-weight: bolder;text-shadow: 
    2px 2px 4px rgba(255, 0, 0, 0.7),
    -2px -2px 4px rgba(0, 255, 0, 0.7),
    2px -2px 4px rgba(0, 0, 255, 0.7),
    -2px 2px 4px rgba(255, 255, 0, 0.7); padding-top:250px;">
      <h1> online examination system </h1>
      <h3>"Transforming Education for the Future"</h3>
    </div>
  </div>

  <div class=".container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <p>
          The Online Examination System (OES) <br>is a web-based platform designed to revolutionize
          the traditional examination process by leveraging digital
          technologies. The system facilitates the creation, administration,
          and evaluation of exams in an
          <br> efficient, secure, and user-friendly manner.
        </p>

        <p>


          The Online Examination System represents
          a modern and efficient approach to academic assessments,
          providing a secure, accessible, and technologically
          advanced platform for educational institutions.
          Its features cater to the evolving needs of administrators,
          instructors, and students, fostering a
          streamlined and reliable examination process.
        </p>

        <a class="btn btn-primary" type="button" href="about.php" class="hbtn" id="btnnav">read more</a>
      </div>
      <div class="col-sm-6"><img src="computer.jpg" style="height: 250px; width:80%;"></div>
    </div>
  </div>




 


  <div class=".container-fluid">
    <div class="section">

      <div class="row">
        <div>
          <h1 style="text-align: center; padding-bottom:50px;">features of online examination </h1>
        </div>

        <div class="col p-3 bg-primary text-white">
          <h2 style="text-align: center;">student emport </h2>
          <div class="card" style="width:400px">
            <img class="card-img-top" src="student111.jpg" alt="Card image" style="width:100%">
            <div class="card-body">
              <p class="card-text">It seems like there might be a slight typo in your
                question, and it's not entirely
                clear what you're referring to with "student emport." </p>
            </div>
          </div>
        </div>
        <div class="col p-3 bg-dark text-white">
          <h2 style="text-align: center;">question setup</h2>
          <div class="card" style="width:400px">
            <img class="card-img-top" src="question.jpeg" alt="Card image" style="width:100%">
            <div class="card-body">
              <p class="card-text">Setting up questions in an online examination system involves creating,
                organizing, and managing a repository of questions that can be used in various exams</p>
            </div>
          </div>
        </div>
        <div class="col p-3 bg-primary text-white">
          <h2 style="text-align: center;">online exam</h2>
          <div class="card" style="width:400px">
            <img class="card-img-top" src="online exam pic.webp" alt="Card image" style="width:100%">
            <div class="card-body">

              <p class="card-text">An online exam refers to the process of
                conducting tests, assessments, or examinations using digital technology
                and the internet. This approach has gained popularity in educational institutions,
                professional certification programs, and various organizations.</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>


<!--our team section-->

  <div class=".container-fluid">
    <div class="section">

      <div class="row">
        <div>
          <h1 style="text-align: center; padding-bottom:50px;">our teachers </h1>
        </div>

        <div class="col p-3 " style="padding-left:10px;" >
          
          <div class="card" style="width:400px;">
            <img class="card-img-top" src="kesav.jpg" alt="Card image" style="width:250px; height:250px; margin:auto; padding:10px; border-radius:100%;">
            <div class="card-body" class="tecdes">
            <h2 style="text-align: center;">keshav paudel</h2>
            <p>Lecturer,Tribhuvan University at Mahendra Multiple College,Nepalgunj,Nepal</p>
            <p>program Coordinator at Kathmandu BernHardt College,Bafal,kalanki</p>
            <p>Volunteer at <b>Nepal Research and Education Netrok</b></p>
            <p>former lecturer at People's campus</p>
            <p>former lecturer at Prime College</p>
            <p>Studied at St.Xavier's College,Maitighar</p>
            <p>Studied at Joseph's College,Triuchirapalli, Tamilnandu,India</p>
            </div>
          </div>
        </div>
        <div class="col p-3"  style="padding-left:10px;">
          <div class="card" style="width:400px;">
            <img class="card-img-top" src="ashok.jpg" alt="Card image" style="width:250px; height:250px; margin:auto; padding:10px; border-radius:100%;">
            <div class="card-body" class="tecdes">
            <h2 style="text-align: center;">ashok chand</h2>
            <p>Lecturer,Tribhuvan University at Mahendra Multiple College,Nepalgunj,Nepal</p>
            <p>Lecturer at Banke Bageshwari Campus Nepalgunj,Nepal</p>
            <p>Former lecturer Nepalgunj Campus of Manegment & Technology </p>
            <p>Former <b>Web Developer at CrossOver Nepal </b></p>
           <p>Studied at SNSC</p>
            <p>Studied at Pokhara University</p>
            <p>Studied at Tribhuvan University of Pune,India</p>
          
            </div>
          </div>
        </div>
        <div class="col p-3  style="padding-left:10px;" >
          <div class="card" style="width:400px;">
            <img class="card-img-top" src="lokendra.jpg" alt="Card image" style="width:250px; height:250px; margin:auto; padding:10px; border-radius:100%;">
            <div class="card-body" class="tecdes">
            <h2 style="text-align: center;">lokendra b.k.</h2>
            <p>Lecturer at Brightland College,Nepalgunj,Nepal</p>
            <p> Assistant Lecturer,Tribhuvan University at Mahendra Multiple College,Nepalgunj,Nepal</p>
            <p>Works at Y.C.I.S. Yashwantro chavan institute os science </p>
            <p>Software <b>Enginner at Software Devlopment Program</b></p>
           
            <p>MCS at Padamashree Dr.D.YPatil A.C.S college Pimpri,Pune</p>
            <p>Studied at University of Pune,India</p>
            <div>
    
            </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>


  <!--teacher discription-->

  <div class=".container-fluid">
    <div class="section"  class="teacherdis">

      <div class="row">
        <div>
          <h5 style="text-align: center; padding-bottom:50px;  font-size: 40px; ">Trusted by over <b>270 000 </b> across the world</h5 >
        </div>

        <div class="col p-3 " style="padding-left:50px;">
          
          
          <h5>20 000+</h5>
          <p>Schools</p>

        
        </div>
        <div class="col p-3"  style="padding-left:10px;">
          
          <h5>1000+</h5>
          <p>Universities</p>
        
          </div>
    
        <div class="col p-3"  style="padding-left:10px;" >
          
          <h5>42 million</h5>
          <p>Exams started</p>
         
        </div>

        </div>

      </div>
    </div>





  <!--subject section-->



  <div class="container-fluid mt-3">
        <div>
          <h4 style="text-align: center; padding-bottom:50px;">but i teach C programming ... will online examination system work for me and my students?</h4>
        </div>
  <div class="row" >
    <div class="col p-3" > <button type="submit" class="btn btn-primary"  id="btnsub">c programming</button></div>
    <div class="col p-3"> <button type="submit" class="btn btn-primary" id="btnsub">digital logic</button></div>
    <div class="col p-3"> <button type="submit" class="btn btn-primary" id="btnsub">digital logic</button</div>
  </div>

  <div class="row" >
    <div class="col p-3" > <button type="submit" class="btn btn-primary"  id="btnsub">c++</button></div>
    <div class="col p-3"> <button type="submit" class="btn btn-primary" id="btnsub">web technology</button></div>
    <div class="col p-3" > <button type="submit" class="btn btn-primary" id="btnsub">advance java</button</div>
  </div>
</div>

<!--footer section-->
<div class=".container-fluid"  style="text-transform: capitalize;">
    <div class="footer  bg-dark">
      <div class="row" class="contact">
        <div class="col">
          <div>
            <h4>online examination system</h4>
          </div>
        </div>
        <div class="col">
          <div>
            <h4>useful links</h4>
            <li class="nav-item">
              <a class="nav-link" href="">home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about.php">about</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="contact.php">contact</a>
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