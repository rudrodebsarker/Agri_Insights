const officerSelect = document.getElementById("officerSelect");
const farmerSelect = document.getElementById("farmerSelect");
const assignmentTableBody = document.getElementById("assignmentTableBody");
const form = document.getElementById("assignmentForm");

async function loadJSON(file) {
  const response = await fetch(file);
  return await response.json();
}

async function init() {
  const officers = await loadJSON("data/tables/officers.json");
  const farmers = await loadJSON("data/tables/farmers.json");
  const assignments = await loadJSON("data/tables/assignments.json");

  // officers.forEach(officer => {
  //   const option = document.createElement("option");
  //   option.value = officer.id;
  //   option.textContent = `${officer.name} (${officer.id})`;
  //   officerSelect.appendChild(option);
  // });

  // farmers.forEach(farmer => {
  //   const option = document.createElement("option");
  //   option.value = farmer.id;
  //   option.textContent = `${farmer.name} (${farmer.id})`;
  //   farmerSelect.appendChild(option);
  // });

  assignments.forEach(entry => addAssignmentRow(entry));
}

function addAssignmentRow({ officerID, farmerID, recommendation }) {
  const tr = document.createElement("tr");
  tr.classList.add("hover:bg-gray-50");

  [officerID, farmerID, recommendation].forEach(text => {
    const td = document.createElement("td");
    td.className = "px-4 py-2 whitespace-nowrap";
    td.textContent = text;
    tr.appendChild(td);
  });

  const actionTd = document.createElement("td");
  actionTd.className = "px-4 py-2 whitespace-nowrap flex gap-3 text-primary";
  actionTd.innerHTML = `
    <button
        class="text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border border-slate-200 rounded-l-lg font-medium px-4 py-2 inline-flex space-x-1 items-center">
        <span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
        </span>
        <span class="hidden md:inline-block">Edit</span>
    </button>
    <button
        class="text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border border-slate-200 rounded-r-lg font-medium px-4 py-2 inline-flex space-x-1 items-center">
        <span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
        </span>
        <span class="hidden md:inline-block">Delete</span>
    </button>
  `;
  tr.appendChild(actionTd);

  assignmentTableBody.appendChild(tr);
}

// form.addEventListener("submit", function (e) {
//   e.preventDefault();

//   const officerID = officerSelect.value;
//   const farmerID = farmerSelect.value;
//   const recommendation = document.getElementById("recommendationInput").value;
//   // const joiningDate = document.getElementById("joiningDateInput").value;
//   // const transferDate = document.getElementById("transferDateInput").value;

//   if (!officerID || !farmerID || !recommendation) {
//     alert("Please fill out all fields.");
//     return;
//   }

//   addAssignmentRow({ officerID, farmerID, recommendation});

//   form.reset();
// });

init();