document.addEventListener('DOMContentLoaded', function () {
    const workerForm = document.getElementById('worker-form');
    const shiftForm = document.getElementById('shift-form');
    const workersList = document.getElementById('workers');
    const shiftsList = document.getElementById('shifts');
    const workerMessage = document.getElementById('worker-message');
    const shiftMessage = document.getElementById('shift-message');

    workerForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        const formData = new FormData(workerForm);
        const data = Object.fromEntries(formData);
        
        const response = await fetch('/workers/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        if (result.error) {
            workerMessage.textContent = result.message;
        } else {
            workerMessage.textContent = 'Worker created successfully';
            workerForm.reset();
        }
    });

    shiftForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        const formData = new FormData(shiftForm);
        const data = Object.fromEntries(formData);

        const response = await fetch('/shifts/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.error) {
            shiftMessage.textContent = result.message;
        } else {
            shiftMessage.textContent = 'Shift created successfully';
            shiftForm.reset();
        }
    });

    document.getElementById('load-workers').addEventListener('click', async function () {
        const response = await fetch('/workers/get');
        const result = await response.json();
    
        if (!result.error) {
            const workersTableBody = document.querySelector('#workers tbody');
            workersTableBody.innerHTML = '';
    
            result.data.forEach(worker => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td data-label="ID">${worker.worker_id}</td>
                    <td data-label="Name">${worker.first_name} ${worker.last_name}</td>
                `;
                workersTableBody.appendChild(tr);
            });
        }
    });
    
    document.getElementById('load-shifts').addEventListener('click', async function () {
        const response = await fetch('/shifts/get');
        const result = await response.json();
    
        if (!result.error) {
            const shiftsTableBody = document.querySelector('#shifts tbody');
            shiftsTableBody.innerHTML = '';
    
            result.data.forEach(shift => {
                const tr = document.createElement('tr');
    
                // Convert Unix timestamps to Date objects
                const date = new Date(shift.date * 1000); 
                const startTime = new Date(shift.start_time * 1000); 
                const endTime = new Date(shift.end_time * 1000); 
    
                // Format Date objects to readable strings
                const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
                const dateReadable = date.toLocaleString('en-US', options);
                const startTimeReadable = startTime.toLocaleString('en-US', options);
                const endTimeReadable = endTime.toLocaleString('en-US', options);
    
                tr.innerHTML = `
                    <td data-label="Shift ID">${shift.id}</td>
                    <td data-label="Worker ID">${shift.worker_id}</td>
                    <td data-label="Date">${dateReadable}</td>
                    <td data-label="Start Time">${startTimeReadable}</td>
                    <td data-label="End Time">${endTimeReadable}</td>
                `;
                shiftsTableBody.appendChild(tr);
            });
        }
    });
    
});
