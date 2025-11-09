document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('attendanceTable');
    const rows = table.querySelectorAll('tbody tr');

    function countAbsences(row) {
        let absences = 0;
        const presenceCheckboxes = row.querySelectorAll('td:nth-child(5), td:nth-child(7), td:nth-child(9), td:nth-child(11), td:nth-child(13), td:nth-child(15)');

        presenceCheckboxes.forEach(function(cell) {
            const checkbox = cell.querySelector('input[type="checkbox"]');
            if (!checkbox.checked) {
                absences++;
            }
        });

        return absences;
    }

    function countParticipations(row) {
        let participations = 0;
        const participationCheckboxes = row.querySelectorAll('td:nth-child(6), td:nth-child(8), td:nth-child(10), td:nth-child(12), td:nth-child(14), td:nth-child(16)');

        participationCheckboxes.forEach(function(cell) {
            const checkbox = cell.querySelector('input[type="checkbox"]');
            if (checkbox.checked) {
                participations++;
            }
        });

        return participations;
    }

    function highlightRow(row, absences) {
        row.classList.remove('highlight-green', 'highlight-yellow', 'highlight-red');

        if (absences < 3) {
            row.classList.add('highlight-green');
        } else if (absences >= 3 && absences <= 4) {
            row.classList.add('highlight-yellow');
        } else if (absences >= 5) {
            row.classList.add('highlight-red');
        }
    }

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

    function updateAllRows() {
        rows.forEach(function(row) {
            updateRow(row);
        });
    }

    updateAllRows();

    const allCheckboxes = table.querySelectorAll('input[type="checkbox"]');
    allCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            updateRow(row);
            saveStatsData();
        });
    });

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

    saveStatsData();
    window.reapplyAttendanceHighlights = function() {
        updateAllRows();
        saveStatsData();
    };
});

(function($){
    $(function(){
        $('#highlightExcellentBtn').on('click', function(){
            $('#attendanceTable tbody tr').each(function(){
                var $row = $(this);
                var absText = $row.find('.absence-count').text();
                var abs = parseInt(String(absText).replace(/\D/g, '')) || 0;
                $row.removeClass('highlight-green highlight-yellow highlight-red highlight-blue dimmed-gray');
                if (abs < 3) {
                    $row.addClass('highlight-blue');
                    $row.stop(true, true).animate({ opacity: 1 }, 600);
                } else {
                    $row.addClass('dimmed-gray');
                    $row.stop(true, true).animate({ opacity: 0.35 }, 600);
                }
            });
        });

        $('#resetColorsBtn').on('click', function(){
            $('#attendanceTable tbody tr').each(function(){
                var $r = $(this);
                $r.removeClass('highlight-blue dimmed-gray');
                $r.stop(true, true).animate({ opacity: 1 }, 400);
            });
            if (typeof window.reapplyAttendanceHighlights === 'function') {
                setTimeout(function(){ window.reapplyAttendanceHighlights(); }, 450);
            }
        });

        $('#searchName').on('input', function(){
            var q = $(this).val().toLowerCase().trim();
            var $rows = $('#attendanceTable tbody tr');
            if (!q) {
                $rows.show();
                return;
            }

            var $matches = $rows.filter(function(){
                var $r = $(this);
                var last = ($r.find('td:nth-child(2)').text() || '').toLowerCase();
                var first = ($r.find('td:nth-child(3)').text() || '').toLowerCase();
                return last.indexOf(q) !== -1 || first.indexOf(q) !== -1;
            });

            $rows.hide();
            $matches.show();
        });
    });
})(jQuery);

