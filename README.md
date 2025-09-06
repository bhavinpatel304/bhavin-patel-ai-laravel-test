## Smart Ticket Triage & Dashboard — Take-Home Task 


## Steps to run app
- Clone the repo
- Run composer install, npm install
- Copy .env.example → .env
- Set environment variables - OpenAI
- add your OpenAI key in CUST_OPENAI_API_KEY env variable
- Run php artisan migrate --seed
- Run npm run dev (or npm run build for production)
- Serve the app (php artisan serve)
- php artisan serve, npm run dev, php artisan queue:work



## Assumptions & Trade-offs
- important urls :  <a href="http://127.0.0.1:8000/" rel="nofollow">Dashboard</a>, <a href="http://127.0.0.1:8000/tickets" rel="nofollow">Tickets</a>
- create enum for status
- predefine categories in Model
- add OpenAI/Laravel for classify
- Use OpenAI/ClientFake | if isClassifyEnabled false
- Theme toggle : light | dark and 
- use chart.js for pie chart
- use .scss for better BEM conventions

## Screens for better understandings
<h4>Dashboard dark</h4>
<img src="/snapshots/dashboard.png" width="400px" />
<br/>
<h4>Dashboard light</h4>
<img src="/snapshots/dashboard-light.png" width="400px" />
<br/>

<h4>Ticket List</h4>
<img src="/snapshots/ticket-list.png" width="400px" />
<br/>

<h4>Ticket Edit</h4>
<img src="/snapshots/edit-ticket.png" width="400px" />
<br/>

<h4>Ticket Edit Inline</h4>
<img src="/snapshots/update-ticket-inline.png" width="400px" />
<br/>

<h4>Ticket New</h4>
<img src="/snapshots/new-ticket.png" width="400px" />
<br/>