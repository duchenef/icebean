# icebean

Presentation:

The Ice Bean is an ISBN search tool that queries multiple websites, trying to retrieve cover images and other book-related information. Pictures are resized so they can be added to a library catalogue (field 996#a in Mandarin or other).

The input must be an ISBN. It can be typed or scanned in the 'ISBN 10 or 13' form field. The application can read ISBNs 10 and 13, EAN and even Amazon ASIN numbers. Nevertheless, depending on how the information is stored in each database, searching for an ISBN 10 or 13 does not alway return exactly the same result. A good recommendation is to search for the ISBN as it's printed on the actual book. It is also useful to sometimes search using the old ISBN 10 for reprinted editions: even if only the modern 13-character ISBN is printed on the book, as the old ISBN 10 is likely to be the one to be stored in some databases. If it's unknown, the ISBN 10 is automatically calculated from the ISBN 13 and shown in the 'Book details' section of the Icebean.

Main features:

1. Searches the given ISBN's book on Google books, Amazon, Librarything, Open Library, and OCLC Classify (optional).
2. Displays and resizes cover images that may be available from these websites.
3. Displays item information with Marc reference for the following fields (when available):

Title
ISBN
Language
Author
Publisher
Date of publication
Number of pages
Dimensions
Dewey number and Dewey edition
Price (converted to CHF)
Summary (description)
FAST subject Headings
Other Features:

- The siZeBean is a tool that can resize JPEG pictures, using either an URL or by uploading a file to the server.
- Pictures are ILS-ready: click on save and you'll get a jpeg file of the requested dimension and named from the book's name and ISBN.
- Descriptions can be saved too (as text files).
- Searchable Library Of Congress relator terms, with codes.
- Searchable FAST heading engine with indication of the facets.
- Ctrl+Click the links in the Expand search area to search for the ISBN in Librarything, Classify, Worldcat, Amazon, BNF. It is also possible to search Nelligan using the author's name.

Options:

- Default resize height is 120, best for Mandarin, but you can change it.
- Search Amazon.fr (default) or us or uk (doesn't make much difference apparently).
- If available, Amazon description can be preferred to Google's (for legal reasons, the Amazon descriptions you can see on their websites may not be shown here).

- Please ignore the 'buy from Amazon' button: this button is compulsory for anyone who uses Amazon APIs.