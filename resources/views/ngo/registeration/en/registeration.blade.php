<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<style>
  .min-logo{
    height: 70px;
    width: 70px;
    text-align: center;
  }
  .min-logo-div{
    width: 100%;
    text-align: center;


  }
  *{
    margin: 0;
    padding: 0;
  }
  .header-text{
    padding: 0;
    margin: 0;
  }
  
</style>
<body>

  <div class="min-logo-div">
        <img src="{{ storage_path('app/public/images/emart.png') }}" class="min-logo" alt="">

        <h4 class="header-text">Islamic Emirate Of Afghanistan</h4>
        <h4  class="header-text">Ministry Of Public Health
      </h4>
        <h4 class="header-text">NGO Registration Form
      </h4>
      <br>
      <h5 class="header-text">Registration Number{{ data->register_no }}</h5>

  </div>


  
</body>
</html>