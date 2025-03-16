<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-image:url("https://img.freepik.com/free-vector/abstract-background-design-dark-green_53876-59276.jpg");
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            padding-bottom: 80px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .service-select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 60px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .service-checkbox {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .service-checkbox:hover {
            background-color: #e9ecef;
        }
        .qty-input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .payment-button {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .payment-button:hover {
            background-color: #45a049;
        }
        .total-charges {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            padding-right: 20px;
        }
        .delete-btn {
            color: #dc3545;
            cursor: pointer;
            padding: 5px;
            border: none;
            background: none;
        }
        .delete-btn:hover {
            color: #c82333;
        }
        .add-service-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .add-service-btn:hover {
            background-color: #0056b3;
        }
        .service-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .home-btn {
            position: absolute;
            bottom: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: #1976d2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .home-btn:hover {
            background-color: #1565c0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Service Management</h1>
        
        <button class="add-service-btn" onclick="addNewService()">
            <i class="fas fa-plus"></i> Add New Service
        </button>
        
        <div class="service-checkboxes" id="serviceCheckboxes">
            <div class="service-checkbox">
                <div>
                    <input type="checkbox" id="selectAll" onchange="toggleAll()">
                    <label for="selectAll">Select All Services</label>
                </div>
            </div>
            <div class="service-checkbox">
                <div>
                    <input type="checkbox" id="none" value="none" onchange="updateTable()">
                    <label for="none">None - ₹0</label>
                </div>
                <button class="delete-btn" onclick="removeService('none')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="service-checkbox">
                <div>
                    <input type="checkbox" id="projector" value="projector" onchange="updateTable()">
                    <label for="projector">Projector - ₹2000</label>
                </div>
                <button class="delete-btn" onclick="removeService('projector')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="service-checkbox">
                <div>
                    <input type="checkbox" id="water" value="water" onchange="updateTable()">
                    <label for="water">Water Bottles - ₹20/bottle</label>
                </div>
                <button class="delete-btn" onclick="removeService('water')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="service-checkbox">
                <div>
                    <input type="checkbox" id="wifi" value="wifi" onchange="updateTable()">
                    <label for="wifi">Wi-Fi - ₹500</label>
                </div>
                <button class="delete-btn" onclick="removeService('wifi')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="service-checkbox">
                <div>
                    <input type="checkbox" id="notepad" value="notepad" onchange="updateTable()">
                    <label for="notepad">Notepad & Pen - ₹50/set</label>
                </div>
                <button class="delete-btn" onclick="removeService('notepad')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="service-checkbox">
                <div>
                    <input type="checkbox" id="audio" value="audio" onchange="updateTable()">
                    <label for="audio">Audio System - ₹1500</label>
                </div>
                <button class="delete-btn" onclick="removeService('audio')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Service ID</th>
                    <th>Service Name</th>
                    <th>Service Cost</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody id="serviceTable">
                <!-- Table content will be dynamically updated -->
            </tbody>
        </table>

        <div class="total-charges" id="totalCharges">Total Service Charges: ₹0</div>
        <button class="home-btn" onclick="window.location.href='admin_homepage.php'">Home</button>
        <button class="payment-button"onclick="window.location.href='Payment_Portal_Client.php'">Proceed to Payment</button>
    </div>

    <script>
        const services = {
            'none': {id: 0, name: 'None', cost: 0, qty: 1},
            'projector': {id: 1, name: 'Projector', cost: 2000, qty: 1},
            'water': {id: 2, name: 'Water Bottles', cost: 20, qty: 100},
            'wifi': {id: 3, name: 'Wi-Fi', cost: 500, qty: 1},
            'notepad': {id: 4, name: 'Notepad & Pen', cost: 50, qty: 50},
            'audio': {id: 5, name: 'Audio System', cost: 1500, qty: 1}
        };

        function addNewService() {
            const serviceName = prompt("Enter service name:");
            if (!serviceName || serviceName.trim() === '') {
                alert("Service name is required!");
                return;
            }
            
            const serviceCost = parseInt(prompt("Enter service cost (in ₹):"));
            if (isNaN(serviceCost) || serviceCost < 0) {
                alert("Please enter a valid cost!");
                return;
            }
            
            const serviceKey = serviceName.toLowerCase().replace(/\s+/g, '_');
            
            // Check if service already exists
            if (services[serviceKey]) {
                alert("A service with this name already exists!");
                return;
            }
            
            const serviceId = Object.keys(services).length;
            
            // Add to services object
            services[serviceKey] = {
                id: serviceId,
                name: serviceName,
                cost: serviceCost,
                qty: 1
            };
            
            // Create and append new service checkbox
            const serviceCheckboxes = document.getElementById('serviceCheckboxes');
            const newServiceDiv = document.createElement('div');
            newServiceDiv.className = 'service-checkbox';
            newServiceDiv.innerHTML = `
                <div>
                    <input type="checkbox" id="${serviceKey}" value="${serviceKey}" onchange="updateTable()">
                    <label for="${serviceKey}">${serviceName} - ₹${serviceCost}</label>
                </div>
                <button class="delete-btn" onclick="removeService('${serviceKey}')">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            serviceCheckboxes.appendChild(newServiceDiv);
        }

        function removeService(serviceKey) {
            if (serviceKey === 'none') {
                alert("Cannot delete the 'None' service!");
                return;
            }
            
            if (confirm(`Are you sure you want to remove ${services[serviceKey].name}?`)) {
                // Remove from services object
                delete services[serviceKey];
                
                // Remove from DOM
                const serviceElement = document.querySelector(`#${serviceKey}`).closest('.service-checkbox');
                serviceElement.remove();
                
                // Update table if the removed service was selected
                updateTable();
            }
        }

        function toggleAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.service-checkbox input[type="checkbox"]:not(#selectAll):not(#none)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            document.getElementById('none').checked = false;
            updateTable();
        }

        function updateTable() {
            const tableBody = document.getElementById('serviceTable');
            const checkboxes = document.querySelectorAll('.service-checkbox input[type="checkbox"]:checked:not(#selectAll)');
            
            let tableContent = '';
            let totalCharges = 0;
            
            checkboxes.forEach(checkbox => {
                const service = services[checkbox.value];
                const totalPrice = service.cost * service.qty;
                totalCharges += totalPrice;
                tableContent += `
                    <tr>
                        <td>${service.id}</td>
                        <td>${service.name}</td>
                        <td>₹${service.cost}</td>
                        <td><input type="number" class="qty-input" value="${service.qty}" min="1" onchange="updateTotalPrice(this, ${service.cost})"></td>
                        <td class="total-price">₹${totalPrice}</td>
                    </tr>
                `;
            });
            
            tableBody.innerHTML = tableContent;
            document.getElementById('totalCharges').textContent = `Total Service Charges: ₹${totalCharges}`;
        }

        function updateTotalPrice(input, cost) {
            const quantity = parseInt(input.value);
            if (isNaN(quantity) || quantity < 1) {
                alert("Please enter a valid quantity!");
                input.value = 1;
                return;
            }
            
            const totalPrice = cost * quantity;
            input.parentElement.nextElementSibling.textContent = `₹${totalPrice}`;
            
            // Update total charges
            const rows = document.querySelectorAll('.total-price');
            let totalCharges = 0;
            rows.forEach(row => {
                totalCharges += parseInt(row.textContent.replace('₹', ''));
            });
            document.getElementById('totalCharges').textContent = `Total Service Charges: ₹${totalCharges}`;
        }
    </script>
</body>
</html>
