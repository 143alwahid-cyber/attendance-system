<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generating PDF...</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body style="margin: 0; padding: 0; background: #f3f4f6;">
    <div id="pdf-content" style="display: none;">
        @include('payroll.pdf')
    </div>
    <div style="display: flex; align-items: center; justify-content: center; height: 100vh; font-family: Arial, sans-serif;">
        <div style="text-align: center;">
            <div style="font-size: 18px; color: #1f2937; margin-bottom: 10px;">Generating PDF...</div>
            <div style="font-size: 14px; color: #6b7280;">Please wait while your payroll statement is being prepared.</div>
        </div>
    </div>
    <script>
        window.onload = function() {
            const element = document.getElementById('pdf-content');
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'Payroll_{{ $employee->employee_id }}_{{ $month->format('Y-m') }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2, 
                    useCORS: true,
                    logging: false,
                    letterRendering: true
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait',
                    compress: true
                }
            };
            
            html2pdf().set(opt).from(element).save().then(function() {
                // Hide loading message and show success briefly
                document.body.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100vh; font-family: Arial, sans-serif;"><div style="text-align: center;"><div style="font-size: 18px; color: #16a34a; margin-bottom: 10px;">✓ PDF Downloaded Successfully</div></div></div>';
                
                // Close if in popup, or just hide if in iframe
                setTimeout(function() {
                    if (window.opener) {
                        window.close();
                    } else {
                        document.body.style.display = 'none';
                    }
                }, 1000);
            }).catch(function(error) {
                console.error('PDF generation error:', error);
                document.body.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100vh; font-family: Arial, sans-serif;"><div style="text-align: center;"><div style="font-size: 18px; color: #dc2626; margin-bottom: 10px;">PDF Generation Failed</div><div style="font-size: 14px; color: #6b7280;">Please try again or use your browser\'s print function.</div></div></div>';
            });
        };
    </script>
</body>
</html>
