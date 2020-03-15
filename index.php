<?php 
//database credentials
define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','');
define('DBNAME','ongeza_test');

try {

	//create PDO connection
	$db = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
	//show error
    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
}   
   
// Save customer details
if(isset($_POST["create_customer"])){
	        $first_name = $_POST['first_name'];
	       $last_name = $_POST['last_name'];
	           $town_name = $_POST['town_name'];
	        $gender = $_POST['gender'];

		 if(empty($first_name) or empty($last_name) or empty($town_name)){
       $smg_data[] = '<div class="error">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                Sorry! details missing.
                            </div>';        		
	   }

	if(empty($smg_data)){ 	   

	try {

		 $stmt = $db->prepare('INSERT INTO customer
		 (first_name,last_name,town_name,gender_id) 
		 VALUES 
		 (:first_name,:last_name,:town_name,:gender_id)');
			$stmt->execute(array(
                ':first_name' => $first_name,	
                ':last_name' => $last_name,					
				':town_name' => $town_name,				
                ':gender_id' => $gender
				));
			$id = $db->lastInsertId('id'); 
			if($id){
$smg_data[] = '<div class="success">                            
                <b>Congratulations</b> Item saved successifully.
               </div>';
	}
}
		catch(PDOException $e) {

		    $smg_data[] = '<div class="alert alert-success" role="alert" />'.$e->getMessage().'</div>';
		}
 }
}

//DELETE CUSTOMER
 $deletecust = @$_GET['del'];
 if(!empty($deletecust)){
	$stmt = $db->prepare('DELETE FROM customer WHERE  id=:id');
			$stmt->execute(array(
                ':id' => $deletecust
				));
header("Refresh:0; index.php");
}
				
// Update customer details
if(isset($_POST["create_customer_update"])){
	        $first_name = $_POST['first_name'];
	       $last_name = $_POST['last_name'];
	           $town_name = $_POST['town_name'];
	        $gender = $_POST['gender'];
			 $cusid = $_POST['cusid'];

		 if(empty($cusid)){
       $smg_data[] = '<div class="error">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                Sorry! details missing.
                            </div>';        		
	   }

	if(empty($smg_data)){ 	   

	try {
		
		 $customer_dis = $db->prepare("UPDATE customer SET first_name=:first_name,last_name=:last_name,town_name=:last_name,gender_id=:gender_id where id=:cusid");
                       $customer_dis->execute(array(
                        ':first_name' => $first_name,	
                        ':last_name' => $last_name,					
				         ':town_name' => $town_name,				
                        ':gender_id' => $gender,
						':cusid' => $cusid
				      ));
                       $row_customer = $customer_dis->fetchALL(PDO::FETCH_ASSOC);
 
			if($id){
header("Refresh:1;index.php");
	}
}
		catch(PDOException $e) {

		    $smg_data[] = '<div class="alert alert-success" role="alert" />'.$e->getMessage().'</div>';
		}
 }
}
 ?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>Ongeza Test </title>
        <style>
            .myform table {
                width: 50%;
            }
            
            .myform .mytr {
                text-align: right;
                font-size: 20px;
            }
            
            input[type=text],
            select {
                width: 100%;
                padding: 12px;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
                resize: vertical;
            }
            
            .myform_button_sub {
                background-color: #4CAF50;
                border: none;
                color: white;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;              
                border-radius: 5px;
            }
            
            .myform_button_upd {
                background-color: #ffbb33;
                border: none;
                color: white;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;               
                border-radius: 5px;
            }
            
            .success {
                background-color: #007E33;
                padding: 20px;
                border-radius: 10px;
                color: #fff;
            }
            
            .error {
                background-color: #CC0000;
                padding: 20px;
                border-radius: 10px;
            }
            
            .container {
                with: 80%;
                text-align: center;
            }
			p{
				padding:10px;
			}
        </style>
        <script>
            function checkFirstname() {
                var a = document.getElementById("first_name").value;

                if (a.length < 3) {
                    alert("First name must be greater than 3 character ");
                    return false;
                }
            }
        </script>
    </head>

    <body>
        <div class="container">
            <?php if(@$_GET['c']==1){ ?>
                <div>
                    <?php //echo @$smg_data;

				if(isset($smg_data)){
					foreach($smg_data as $error){
						echo $error;

					}
				}
               ?>
                </div>
				<?php 
				$updateid = @$_GET['edit'];
				if(@$updateid>0){                   
					  $customer_sel = $db->prepare("SELECT customer.id,customer.first_name,customer.last_name,customer.town_name,
                       Gender.gender_name,customer.gender_id  FROM customer left join gender on gender.id=customer.gender_id WHERE customer.id=:id");
                       $customer_sel->execute(array(                       				
                        ':id' => $updateid
				      ));
                       $row_disp = $customer_sel->fetch(PDO::FETCH_ASSOC);   


					?>
                <form class="myform" method="post" onsubmit="return checkFirstname()" action="index.php">
                    <table align="center">
                        <caption><h3>Update Customer(<?php echo $row_disp['first_name'].' '.$row_disp['last_name']; ?>)</h3></caption>
                        <tr>
                            <td class="mytr">First name</td>
                            <td>
                                <input type="text" name="first_name" id="first_name" value="<?php echo $row_disp['first_name']; ?>" required>
								<input type="hidden" name="cusid" value="<?php echo $row_disp['id']; ?>" >
                            </td>
                        </tr>
                        <tr>
                            <td class="mytr">Last name</td>
                            <td>
                                <input type="text" name="last_name" id="last_name" value="<?php echo $row_disp['last_name']; ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="mytr">Town name</td>
                            <td>
                                <input type="text" name="town_name" id="town_name" value="<?php echo $row_disp['town_name']; ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="mytr">Gender</td>
                            <td>
                                <select name="gender" required>
                                    <option value="<?php echo $row_disp['gender_id']; ?>"><?php echo $row_disp['gender_name']; ?></option>
                                    <?php $gender_dis = $db->prepare("SELECT * FROM   gender");
          $gender_dis->execute();
         $row_gender = $gender_dis->fetchALL(PDO::FETCH_ASSOC);
        foreach($row_gender as $gender_control){ ?>
                                        <option value="<?php echo $gender_control['id']; ?>">
                                            <?php echo $gender_control['gender_name']; ?>
                                        </option>
                                        <?php } ?>
                                </select>

                            </td>
                        </tr>

                        <tr><td>                            
                                
                            </td>
                            <td><input type="submit" name="create_customer_update" class="myform_button_upd" value="Update Customer"></td>
                            
                        </tr>
                    </table>
                </form>
                <?php }
                    else{
				?>		
				
                <form class="myform" method="post" onsubmit="return checkFirstname()" action="index.php">
                    <table align="center">
                        <caption><h2>Create new customer</h2></caption>
                        <tr>
                            <td class="mytr">First name</td>
                            <td>
                                <input type="text" name="first_name" id="first_name" placeholder="first name" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="mytr">Last name</td>
                            <td>
                                <input type="text" name="last_name" id="last_name" placeholder="Last name" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="mytr">Town name</td>
                            <td>
                                <input type="text" name="town_name" id="town_name" placeholder="Town name" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="mytr">Gender</td>
                            <td>
                                <select name="gender" required>
                                    <option value="">Select Gender</option>
                                    <?php $gender_dis = $db->prepare("SELECT * FROM   gender");
          $gender_dis->execute();
         $row_gender = $gender_dis->fetchALL(PDO::FETCH_ASSOC);
        foreach($row_gender as $gender_control){ ?>
                                        <option value="<?php echo $gender_control['id']; ?>">
                                            <?php echo $gender_control['gender_name']; ?>
                                        </option>
                                        <?php } ?>
                                </select>

                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td align="left;">
                                <input type="submit" name="create_customer" class="myform_button_sub" value="Create Customer">                              
                            </td>
                        </tr>
                    </table>
                </form> 
				
				<?php	}


				} else{ ?>
                    <a name="create_customer" href="index.php?c=1" class="myform_button_sub">Create Customer</a><br>
                    <table align="center" style="width:50%;  border: 1px solid black; text-align:left; padding:10px; margin-top:10px; ">
                        
						<?php $customer_dis = $db->prepare("SELECT customer.id,customer.first_name,customer.last_name,customer.town_name,
                       Gender.gender_name  FROM customer left join gender on gender.id=customer.gender_id ");
                        $customer_dis->execute();
                       $row_customer = $customer_dis->fetchALL(PDO::FETCH_ASSOC);
					    $rowcounts = $customer_dis->rowCount();
						if($rowcounts>0){ ?>
                        <tr>
                            <th>Id</th>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Town name</th>
							<th colspan="2" align="left">Gender</th>
                        </tr>
						<?php 
						foreach($row_customer as $customer_control){ ?>
                                        
                        <tr>
						    <td><?php echo $customer_control['id']; ?></td>
                            <td><?php echo $customer_control['first_name']; ?></td>
                            <td><?php echo $customer_control['last_name']; ?></td>
                            <td><?php echo $customer_control['town_name']; ?></td>
							<td><?php echo $customer_control['gender_name']; ?></td>
                           <td>
						        <a  href="index.php?c=1&edit=<?php echo $customer_control['id']; ?>" class="myform_button_sub_edit">Edit</a>
						        <a  href="index.php?del=<?php echo $customer_control['id']; ?>" class="myform_button_sub_delete">Delete</a>
						   </td>							
                        </tr>
						<?php }}
                          else{
							  echo "<p>WHOOPS! Currently there is no customer to display <br> Please click the button above to create new customer</p>";
						  }
						?>
                    </table>
                    <?php } ?>
        </div>
    </body>

    </html>
