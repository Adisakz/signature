<?php

function resizeImage($tmp_name, $img_name, $img_size, $dir, $new_width, $new_height) {
    list($width, $height) = getimagesize($tmp_name);
    $tmp_img = imagecreatetruecolor($new_width, $new_height);
  
    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
    $img_ex_lc = strtolower($img_ex);
  
    switch ($img_ex_lc) {
        case 'jpg':
        case 'jpeg':
            $source = imagecreatefromjpeg($tmp_name);
            break;
        case 'png':
            $source = imagecreatefrompng($tmp_name);
            break;
        default:
            echo "สกุลไม่ถูกต้อง";
            exit();
    }
  
    imagecopyresampled($tmp_img, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
  
    $resized_img_name = uniqid("RESIZED_IMG-", true) . '.' . $img_ex_lc;
    $resized_path = $dir . $resized_img_name;
  
    switch ($img_ex_lc) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($tmp_img, $resized_path);
            break;
        case 'png':
            imagepng($tmp_img, $resized_path);
            break;
    }
  
    imagedestroy($source);
    imagedestroy($tmp_img);
  
    return $resized_img_name;
  }
  
  
  error_reporting(E_ERROR | E_PARSE);


if (isset($_POST['submit'])) {
    $Option = $_POST['signature'];
    $name = 'Name Test';
    if ($Option == 2) {
        require './popup/popup.php';
        
        ?>
             <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '<h4 class="t1"><strong>ลงนามผู้รับรอง</strong></h4>',
                    html: '<center>'+
                    '<div class="mb-3">'+
                    '<div class="mb-3">'+
                    '<label class="form-label" for="imp_sig">'+
                    '</label><div id="canvasDiv">'+
                    '</div><br>'+
                    '<button type="button" class="btn btn-danger" id="reset-btn">Clear</button>'+
                    '&nbsp;&nbsp;&nbsp;&nbsp;'+
                    '<button type="button" class="btn btn-success" id="btn-save">Save</button>'+
                    '</div> <form id="signatureform" action="" style="display:none" method="post">'+ // link กลับมาหน้าเดิม
                    '<input type="hidden" id="signature" name="signature">'+
                    '<input type="hidden" name="signaturesubmit" value="1">'+
                    '<input type="hidden" name="data1" value="<?php echo $name?>">'+//ส่งค่าไปอีกฟังก์ชั่นที่นี้
                    '</form></div></center>',
                    confirmButtonText: '<div class="text t1">ออก</div>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php';
                    }
                });
            });
        </script>
        
          <?php 
      } else if ($Option == 1) {   
        // ทำการบันทึกภาพ
        $img_name = $_FILES["image-signature-report"]["name"];
        $img_size = $_FILES["image-signature-report"]["size"];
        $tmp_name = $_FILES["image-signature-report"]["tmp_name"];
        $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);
        $allowed_exs = array("jpg", "jpeg", "png");
    
        // เช็คว่าสกุลของไฟล์อยู่ในรายการที่อนุญาตหรือไม่
        if (in_array($img_ex_lc, $allowed_exs)) {
            if ($img_size > 125000) {
                // ทำการ resize และบันทึกภาพ
                $resized_img_name = resizeImage($tmp_name, $img_name, $img_size, './image_signature/', 600, 900);
                move_uploaded_file($resized_img_name, $dir);
                echo '<script>alert("บันทึกเรียบร้อย");</script>';
                // INSERT into database
                //$sql = "UPDATE repair_report_pd05 SET id_head='$id_person',send_to = '$id_person_send', date_update_head = CURRENT_TIMESTAMP,signature_head= '$resized_img_name', status='4' WHERE id_repair=$idRepair";
                //mysqli_query($conn, $sql);
                //header("location:./repair?success=success");
            } else {
                // ทำการบันทึกไฟล์ที่ไม่ต้อง resize
                $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                $dir = './image_signature/' . $new_img_name;
                move_uploaded_file($tmp_name, $dir);
                echo '<script>alert("บันทึกเรียบร้อย");</script>';
                // INSERT into database
                //$sql = "UPDATE repair_report_pd05 SET id_head='$id_person',send_to = '$id_person_send', date_update_head = CURRENT_TIMESTAMP,signature_head= '$new_img_name', status='4' WHERE id_repair=$idRepair";
                //mysqli_query($conn, $sql);
                //header("location:./repair?success=success");
            }
        } else {
            echo '<script>alert("สกุลไม่ถูกต้อง");</script>';
      }
        }
    }
    
    //ตอนที่ 2 เมื่อลงนามแล้วกด save ในฟอร์ม popup ลงนามแล้วให้ บันทึกลงฐานข้อมูล ------------------------------------------------------------------
    if (isset($_POST['signaturesubmit'])) {
      $signature = $_POST['signature'];
      $signatureFileName = uniqid() . '.png';
      $signature = str_replace('data:image/png;base64,', '', $signature);
      $signature = str_replace(' ', '+', $signature);
      $data = base64_decode($signature);
      $file = './image_signature/' . $signatureFileName;
      file_put_contents($file, $data);
    
      
        //$sql = "UPDATE repair_report_pd05 SET id_head='$id_person',send_to = '$data1', date_update_head = CURRENT_TIMESTAMP,signature_head= '$signatureFileName', status='4' WHERE id_repair=$data2";
        //mysqli_query($conn, $sql);
        //header("location: ./repair?success=success");
        echo '<script>alert("บันทึกเรียบร้อย");</script>';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>signature</title>
    <link rel="stylesheet" href="./css/canvas.css">
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="./dist/css/adminlte.min.css">
    <script src="./libs/modernizr.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <form id="quickForm" action="" method="post" enctype="multipart/form-data">
        <div class="card-body">
            <div class="form-group" id="my-form-group">
              <label for="signature">ลายซ็น :</label> <br> 
              
              <!-- radio buttons -->
              <input type="radio" name="signature" id="option-with-image" value="1" required> 
              <label for="option-with-image" style="font-weight: normal;">เลือกจากรูปภาพในเครื่อง</label><br>

              <input type="radio" name="signature" id="option-without-image" value="2" required>
              <label for="option-without-image" style="font-weight: normal;">เซ็นตอนนี้</label>

              <!-- ช่อง input สำหรับอัพโหลดภาพ -->
              <div id="image-upload" style="display: none;">
                  <label for="image">อัพโหลดรูปภาพ:</label>
                  <input type="file" id="image-signature-report" name="image-signature-report">
              </div>
 
         </div>
         <button type="submit" name="submit" class="btn btn-primary">ตกลง</button>
        </div>

           
  
    </form>
    <script src="./dist/js/canvas.js"></script>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add an event listener to the radio buttons
        document.querySelectorAll('input[name="signature"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                // Check if the radio button with value "1" is selected
                if (this.value === '1') {
                    // Show the image upload section
                    document.getElementById('image-upload').style.display = 'block';
                } else {
                    // Hide the image upload section
                    document.getElementById('image-upload').style.display = 'none';
                }
            });
        });
    });
    </script>

