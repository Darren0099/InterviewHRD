# TODO: Implement Delete All and Download PDF Features

## Step 1: Install pdfkit dependency
- Add pdfkit to package.json dependencies
- Run npm install to install the new dependency

## Step 2: Update server.js
- Add /delete-all endpoint to delete all data from interviews table
- Add /all-data endpoint to fetch all interview data for PDF generation
- Add /download-pdf endpoint to generate and send PDF with all names and evaluations per division

## Step 3: Update public/index.html
- Add "Delete All" button in the top-left corner
- Add "Download PDF" button next to it

## Step 4: Update public/app.js
- Add event listeners for deleteAllBtn and downloadPdfBtn
- Implement delete all functionality with confirmation
- Implement PDF download functionality

## Step 5: Test the functionality
- Test delete all button
- Test PDF download button
- Ensure PDF includes all data across divisions
