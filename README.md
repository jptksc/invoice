# Invoice
A simple flat-file invoicing solution for Stripe.

![Alt text](screenshot.jpg?raw=true)

## Description

Invoice is a complete invoicing solution that allows you to create any number invoices for any number of clients and get paid via Stripe. Configure your preferences, upload to any PHP compatible server (SSL required for Stripe payments), create clients and invoices using simple text files — no database or CMS required.

## Setup & Installation

1. Configure your "Invoice", "Currency" and "Stripe" settings within "settings.php".
2. Upload to any PHP compatible server (SSL required).

## Creating Clients

You can create any number of clients using simple text files. The “content/clients” folder includes a sample client file, but continue reading for more details below on creating your own clients:

1. Create a new text file using the editor of your choice.
2. On the 1st line, add the client’s name (e.g. “John Doe”).
3. On the 2nd line, add the client’s email address (e.g. “john@johndoe.com”).
4. Save the file with a unique ID (e.g. “john-doe.txt”) within the “clients” folder.

## Creating Invoices

You can create any number of invoices using simple text files. The “content/invoices” folder includes a sample invoice file, but continue reading for more details below on creating your own invoices:

1. Create a new text file using the editor of your choice.
2. On the 1st line, add the invoice due date (e.g. “June 26th, 2015”).
3. On the 2nd line, add the invoice status “Not Paid” (this must be typed correctly, otherwise things will break).
4. On the 4th line, start adding your invoice items (line by line) using the following format:
    - Invoice Item Description. - Unit of Measure - Cost
    - (e.g. “Communications and meetings. - 6 Hours - 1050.00”)
5. Save the file with a unique ID (e.g. “000123.txt”) within the “invoices” folder.

## Creating Invoice URL’s

Now that you’ve created at least one client and at least one invoice, you can generate an invoice URL to sent to your client. For instance, if the client file was “john-doe.txt” and the invoice file was “000123.txt”, you would use the following format for your URL:

- https://yoursite.com/?client=john-doe&invoice=000123

## Services

If you need help developing the functionality you need for your own site, I'm available for hire to help you with whatever customization or implementation services you might need. To get started, just send me an email (jason@circa75.co).

## Version 1.0

- NEW: Initial release.

## License

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
