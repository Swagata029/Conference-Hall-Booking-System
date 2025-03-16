<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Management</title>
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
    </style>
</head>
<body>
    <?php
    $room_id = isset($_GET['room_id']) ? $_GET['room_id'] : '';
    $hall_id = isset($_GET['hall_id']) ? $_GET['hall_id'] : '';
    ?>
    <div class="container">
        <h1>Service Management</h1>
        
        <div class="service-checkboxes">
            <div class="service-checkbox">
                <input type="checkbox" id="selectAll" onchange="toggleAll()">
                <label for="selectAll">Select All Services</label>
            </div>
            <div class="service-checkbox">
                <input type="checkbox" id="none" value="none" onchange="updateTable()">
                <label for="none">None - ₹0</label>
            </div>
            <div class="service-checkbox">
                <input type="checkbox" id="projector" value="projector" onchange="updateTable()">
                <label for="projector">Projector - ₹2000</label>
            </div>
            <div class="service-checkbox">
                <input type="checkbox" id="water" value="water" onchange="updateTable()">
                <label for="water">Water Bottles - ₹20/bottle</label>
            </div>
            <div class="service-checkbox">
                <input type="checkbox" id="wifi" value="wifi" onchange="updateTable()">
                <label for="wifi">Wi-Fi - ₹500</label>
            </div>
            <div class="service-checkbox">
                <input type="checkbox" id="notepad" value="notepad" onchange="updateTable()">
                <label for="notepad">Notepad & Pen - ₹50/set</label>
            </div>
            <div class="service-checkbox">
                <input type="checkbox" id="audio" value="audio" onchange="updateTable()">
                <label for="audio">Audio System - ₹1500</label>
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
        <button class="payment-button" onclick="proceedToPayment()">Proceed to Payment</button>
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
                    <tr data-service="${service.name}">
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
            const quantity = input.value;
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

        function proceedToPayment() {
            const roomId = '<?php echo $room_id; ?>';
            const hallId = '<?php echo $hall_id; ?>';
            const selectedServices = [];
            const checkboxes = document.querySelectorAll('.service-checkbox input[type="checkbox"]:checked:not(#selectAll)');
            
            let servicesData = {
                room_id: roomId,
                hall_id: hallId,
                services: [],
                total_amount: 0
            };

            checkboxes.forEach(checkbox => {
                if(checkbox.value !== 'none') {
                    const service = services[checkbox.value];
                    const row = document.querySelector(`tr[data-service="${service.name}"]`);
                    const qtyInput = row ? row.querySelector('.qty-input') : null;
                    const quantity = qtyInput ? parseInt(qtyInput.value) : service.qty;
                    const totalPrice = service.cost * quantity;

                    servicesData.services.push({
                        id: service.id,
                        name: service.name,
                        cost: service.cost,
                        quantity: quantity,
                        total: totalPrice
                    });
                    servicesData.total_amount += totalPrice;
                }
            });

            const servicesParam = encodeURIComponent(JSON.stringify(servicesData));
            window.location.href = `Payment_Portal_Client.php?services=${servicesParam}`;
        }
    </script>
</body>
</html>
