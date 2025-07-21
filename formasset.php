<?php
  ob_start();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  
  require_once('newutil2.php');
  $pdo = getPDONew()  ;	

  $sql = "select * from pageTradeStatus"; 		
  $params = array();
  $row = pdoRowSet($sql,$params,$pdo) ;
  echo  $row['assetCode'] . '|'. $row['isopenTrade'].'|'. $row['moneyTrade'] .'|'. $row['targetTrade'];
  $isChecked = '' ; $txt = 'ปิด' ;
  if ($row['isopenTrade'] === 'Y') {
	  $isChecked = 'checked' ;
	  $txt = 'เปิด' ;
  }

  $isMartingale= '' ; $txt2 = 'ปิด' ;
  if ($row['isMartingale'] === 'Y') {
	  $isMartingale = 'checked' ;
	  $txt2 = 'เปิด' ;
  }

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading Settings Form</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2.5rem;
            margin: 2rem 0;
        }
        
        .form-title {
            color: #667eea;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.75rem;
        }
        
        .form-select, .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-check {
            padding: 1rem 0;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:disabled {
            opacity: 0.6;
            transform: none;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            margin-top: 1rem;
        }
        
        .loading-spinner {
            display: none;
        }
        
        @media (max-width: 768px) {
            .form-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <div class="form-container">
                    <h2 class="form-title">ตั้งค่าการเทรด</h2>
                    
                    <form id="tradeForm">
                        <!-- ชื่อสินทรัพย์ -->
						<?php
						  $assetArray = array('R_25','R_50','R_75','R_100') ;
					     ?>
                        <div class="mb-4">
                            <label for="assetName" class="form-label">ชื่อสินทรัพย์</label>
                            <select class="form-select" id="assetName" name="assetName" required>
                                <option value="">เลือกสินทรัพย์</option>
								<?php
								  for ($i=0;$i<=count($assetArray)-1;$i++) {  
									  $isSelected = ($row['assetCode'] == $assetArray[$i]) ? ' selected ' : '' ;
									  ?>
                                    <option value="<?=$assetArray[$i]?>" <?=$isSelected?>><?=$assetArray[$i]?>
									</option>
							     <?php
								  }
								?>
								 

								 
                            </select>
                        </div>
                        
                        <!-- เปิดเทรด Switch -->
                        <div class="mb-4">
                            <label class="form-label">เปิดเทรด?</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enableTrade" name="enableTrade" style="transform: scale(1.5);margin-left:0.5em" <?=$isChecked?>>
                                <label class="form-check-label ms-3" for="enableTrade">
                                    <span id="tradeStatus"><?=$txt?></span>
                                </label>
                            </div>
                        </div>

						<!-- เปิดเทรด Switch -->
                        <div class="mb-4">
                            <label class="form-label">MartinGale?</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enableMartingale" name="enableMartingale" style="transform: scale(1.5);margin-left:0.5em" <?=$isMartingale?>>
                                <label class="form-check-label ms-3" for="enableMartingalue">
                                    <span id="tradeMartingale"><?=$txt2?></span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- ราคาเทรดต่อครั้ง -->
                        <div class="mb-4">
                            <label for="tradeAmount" class="form-label">ราคาเทรดต่อครั้ง (USD)</label>
                            <input type="number" class="form-control" id="tradeAmount" name="tradeAmount" value='<?=$row['moneyTrade']?>'
                                   min="1" step="0.01" placeholder="กรอกจำนวนเงินต่อการเทรด" required>
                        </div>
                        
                        <!-- เงินเป้าหมาย -->
                        <div class="mb-4">
                            <label for="targetAmount" class="form-label">เงินเป้าหมาย (USD)</label>
                            <input type="number" class="form-control" id="targetAmount" name="targetAmount" 
                                   min="0.5" step="0.01" placeholder="กรอกเป้าหมายกำไร" 
								   value='<?=$row['targetTrade']?>'
								   required>
                        </div>
                        
                        <!-- ปุ่มบันทึก -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <span class="loading-spinner spinner-border spinner-border-sm me-2" role="status"></span>
                                <span class="btn-text">บันทึกการตั้งค่า</span>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Alert Messages -->
                    <div id="alertContainer"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle switch text
        document.getElementById('enableTrade').addEventListener('change', function() {
            const statusText = document.getElementById('tradeStatus');
            statusText.textContent = this.checked ? 'เปิด' : 'ปิด';
        });
        
        // Show alert function
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alertDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Form submission
        document.getElementById('tradeForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const saveBtn = document.getElementById('saveBtn');
            const spinner = saveBtn.querySelector('.loading-spinner');
            const btnText = saveBtn.querySelector('.btn-text');
            
            // Show loading state
            spinner.style.display = 'inline-block';
            btnText.textContent = 'กำลังบันทึก...';
            saveBtn.disabled = true;
            
            // Clear previous alerts
            document.getElementById('alertContainer').innerHTML = '';
            
            // Collect form data
            const formData = new FormData();
            formData.append('assetName', document.getElementById('assetName').value);
            formData.append('enableTrade', document.getElementById('enableTrade').checked ? '1' : '0');

			formData.append('enableMartingale', document.getElementById('enableMartingale').checked ? '1' : '0');

            formData.append('tradeAmount', document.getElementById('tradeAmount').value);
            formData.append('targetAmount', document.getElementById('targetAmount').value);
            
            try {
                const response = await fetch('https://thepapers.in/deriv/savesettrade.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const result = await response.text();
                    showAlert('บันทึกการตั้งค่าเรียบร้อยแล้ว! ' + result, 'success');
                    
                    // Optional: Reset form after successful submission
                    // this.reset();
                    // document.getElementById('tradeStatus').textContent = 'ปิด';
                } else {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง', 'danger');
            } finally {
                // Reset button state
                spinner.style.display = 'none';
                btnText.textContent = 'บันทึกการตั้งค่า';
                saveBtn.disabled = false;
            }
        });
        
        // Number input validation
        document.getElementById('tradeAmount').addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
        });
        
        document.getElementById('targetAmount').addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
        });
    </script>
</body>
</html>