// attendance calculations and highlighting

document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('attendanceTable');
    const rows = table.querySelectorAll('tbody tr');
    
    // count how many absences
    function countAbsences(row) {
        let absences = 0;
        const presenceCheckboxes = row.querySelectorAll('td:nth-child(5), td:nth-child(7), td:nth-child(9), td:nth-child(11), td:nth-child(13), td:nth-child(15)');
        
        presenceCheckboxes.forEach(function(cell) {
            const checkbox = cell.querySelector('input[type="checkbox"]');
            // Only count if checkbox is not disabled (has attendance data)
            if (checkbox && !checkbox.disabled && !checkbox.checked) {
                absences++;
            }
        });
        
        return absences;
    }
    
    // count participations
    function countParticipations(row) {
        let participations = 0;
        const participationCheckboxes = row.querySelectorAll('td:nth-child(6), td:nth-child(8), td:nth-child(10), td:nth-child(12), td:nth-child(14), td:nth-child(16)');
        
        participationCheckboxes.forEach(function(cell) {
            const checkbox = cell.querySelector('input[type="checkbox"]');
            // Only count if checkbox is checked (ignore disabled ones)
            if (checkbox && !checkbox.disabled && checkbox.checked) {
                participations++;
            }
        });
        
        return participations;
    }
    
    // change row color based on absences
    function highlightRow(row, absences) {
        row.classList.remove('highlight-green', 'highlight-yellow', 'highlight-red');
        
        if (absences < 3) {
            row.classList.add('highlight-green');
        } else if (absences >= 3 && absences <= 4) {
            row.classList.add('highlight-yellow');  // warning
        } else if (absences >= 5) {
            row.classList.add('highlight-red');  // too many!
        }
    }
    
    // create message for student
    function generateMessage(absences, participations) {
        if (absences < 3 && participations >= 4) {
            return 'Good attendance - Excellent participation';
        } else if (absences >= 5) {
            return 'Excluded - too many absences - You need to participate more';
        } else if (absences >= 3 && participations < 3) {
            return 'Warning - attendance low - You need to participate more';
        } else if (absences >= 3) {
            return 'Warning - attendance low - You need to participate more';
        } else if (participations < 3) {
            return 'You need to participate more';
        } else {
            return 'Good attendance';
        }
    }
    
    // update row when checkbox changes
    function updateRow(row) {
        const absences = countAbsences(row);
        const participations = countParticipations(row);
        
        const absenceCell = row.querySelector('.absence-count');
        absenceCell.textContent = absences + ' Abs';
        
        const participationCell = row.querySelector('.participation-count');
        participationCell.textContent = participations + ' Par';
        
        highlightRow(row, absences);
        
        const messageCell = row.querySelector('.message-cell');
        messageCell.textContent = generateMessage(absences, participations);
    }
    
    // update all rows at once
    function updateAllRows() {
        rows.forEach(function(row) {
            updateRow(row);
        });
    }
    
    // run on page load
    updateAllRows();
    
    // listen for checkbox changes
    const allCheckboxes = table.querySelectorAll('input[type="checkbox"]');
    allCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            updateRow(row);
            saveStatsData(); // update stats when data changes
        });
    });
    
    // save stats data for reports page
    function saveStatsData() {
        const students = [];
        rows.forEach(function(row) {
            const absences = countAbsences(row);
            const participations = countParticipations(row);
            let status = 'green';
            if (absences >= 5) {
                status = 'red';
            } else if (absences >= 3) {
                status = 'yellow';
            }
            students.push({
                absences: absences,
                participations: participations,
                status: status
            });
        });
        
        const data = {
            students: students,
            total: students.length
        };
        
        localStorage.setItem('attendanceData', JSON.stringify(data));
    }
    
    // save initial data
    saveStatsData();
});

