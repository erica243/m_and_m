function print_receipt() {
    // Clone the contents of the container
    var container = document.querySelector('.container-fluid').cloneNode(true);
    
    // Remove unwanted elements from the cloned container
    container.querySelectorAll('.logout, .mm-cake-ordering').forEach(function(element) {
        element.remove();
    });

    // Get the updated HTML content after removal
    var printContents = container.innerHTML;
    
    // Open a new window for printing
    var receiptWindow = window.open('', '', 'height=600,width=800,location=no');

    // URL of your logo image (ensure this path is correct)
    var logoUrl = 'img/your/logo.png'; // Update this path to your actual logo

    // Write the HTML content to the new window
    receiptWindow.document.write('<html><head><title>Receipt</title>');
    receiptWindow.document.write('<style>');
    receiptWindow.document.write('table { border-collapse: collapse; width: 100%; }');
    receiptWindow.document.write('th, td { border: 1px solid black; padding: 8px; text-align: left; font-size: 14px; }');
    receiptWindow.document.write('th { background-color: #f2f2f2; }');
    receiptWindow.document.write('body { font-size: 12px; }');
    receiptWindow.document.write('@media print { body { font-size: 10px; } }');
    receiptWindow.document.write('</style></head><body>');
    
    // Header Section
    receiptWindow.document.write('<div style="text-align: center; margin-bottom: 20px;">');
    receiptWindow.document.write('<img src="' + logoUrl + '" alt="Logo" style="max-width: 150px;">');
    receiptWindow.document.write('<h1 style="font-size: 20px; text-align: center;">Receipt</h1>');
    receiptWindow.document.write('<p>Order Receipt</p>');
    receiptWindow.document.write('<p>Receipt Number: 20240521-5788385448286862261</p>');
    receiptWindow.document.write('<p>Receipt Date: 21/05/2024 10:13:36</p>');
    receiptWindow.document.write('<p>Sold By: BSD Best Seller Dress</p>');
    receiptWindow.document.write('</div>');
    
    // Delivery Details
    receiptWindow.document.write('<h3>Delivery Details</h3>');
    receiptWindow.document.write('<p>Erica Adlit</p>');
    receiptWindow.document.write('<p>sitio kagwangan, Madridejos, purok santan, Tarong, Madridejos, Cebu, Philippines</p>');
    
    // Order Details
    receiptWindow.document.write('<h3>Order Details</h3>');
    receiptWindow.document.write('<p>Order Number: 5788385448286862261</p>');
    receiptWindow.document.write('<p>Order Date: 15/05/2024 22:07:10</p>');
    
    // Table with Order Items
    receiptWindow.document.write('<table>' + printContents + '</table>');
    
    // Log Out Button
    receiptWindow.document.write('<div style="text-align: center; margin-top: 20px;">');
    receiptWindow.document.write('<button onclick="window.location.href=\'logout_url_here\';">Log Out</button>'); // Replace 'logout_url_here' with your actual log out URL
    receiptWindow.document.write('</div>');

    // Footer
    receiptWindow.document.write('<div style="margin-top: 20px;">');
    receiptWindow.document.write('<p>This receipt serves as proof of purchase and does not qualify as a tax invoice.</p>');
    receiptWindow.document.write('<p>Thank you for your purchase.</p>');
    receiptWindow.document.write('</div>');
    
    receiptWindow.document.write('</body></html>');
    
    receiptWindow.document.close();
    receiptWindow.print();
}
