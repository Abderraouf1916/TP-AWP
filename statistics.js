//calculate and display statistics

document.addEventListener('DOMContentLoaded', function() {
    
    //get data 
    function getAttendanceData() {
        const savedData = localStorage.getItem('attendanceData');
        if (savedData) {
            try {
                return JSON.parse(savedData);
            } catch(e) {
                console.log('could not parse saved data');
            }
        }
        
        //default data 
        const students = [
            { absences: 5, participations: 1, status: 'red' },
            { absences: 1, participations: 4, status: 'green' },
            { absences: 4, participations: 2, status: 'yellow' }
        ];
        
        return {
            students: students,
            total: students.length
        };
    }
    
    //calculate statistics
    function calculateStats(data) {
        let good = 0;
        let warning = 0;
        let excluded = 0;
        let totalAbsences = 0;
        let totalParticipations = 0;
        
        data.students.forEach(function(student) {
            if (student.absences < 3) {
                good++;
            } else if (student.absences >= 3 && student.absences <= 4) {
                warning++;
            } else {
                excluded++;
            }
            
            totalAbsences += student.absences;
            totalParticipations += student.participations;
        });
        
        const total = data.total || data.students.length;
        const avgAbsences = total > 0 ? (totalAbsences / total).toFixed(1) : 0;
        const avgParticipation = total > 0 ? (totalParticipations / total).toFixed(1) : 0;
        const goodPercentage = total > 0 ? Math.round((good / total) * 100) : 0;
        const participationPercentage = total > 0 ? Math.round((totalParticipations / (total * 6)) * 100) : 0; // max 6 sessions
        
        return {
            good: good,
            warning: warning,
            excluded: excluded,
            total: total,
            avgAbsences: avgAbsences,
            avgParticipation: avgParticipation,
            goodPercentage: goodPercentage,
            participationPercentage: participationPercentage
        };
    }
    
    //donut chart
    function drawDonutChart(svgId, segmentId, percentage, color) {
        const circumference = 2 * Math.PI * 80; // radius is 80
        const offset = circumference - (percentage / 100) * circumference;
        
        const segment = document.getElementById(segmentId);
        if (segment) {
            segment.setAttribute('stroke', color);
            segment.setAttribute('stroke-dasharray', `${circumference * (percentage / 100)} ${circumference}`);
        }
    }
    
    //update display
    function updateStats() {
        const data = getAttendanceData();
        const stats = calculateStats(data);
        
        //update attendance chart
        const goodColor = '#66BB6A';
        drawDonutChart('attendanceChart', 'attendanceSegment', stats.goodPercentage, goodColor);
        document.getElementById('attendanceValue').textContent = stats.goodPercentage + '%';
        document.getElementById('goodCount').textContent = stats.good;
        document.getElementById('warningCount').textContent = stats.warning;
        document.getElementById('excludedCount').textContent = stats.excluded;
        
        //update participation chart
        const participationColor = '#81C784';
        drawDonutChart('participationChart', 'participationSegment', stats.participationPercentage, participationColor);
        document.getElementById('participationValue').textContent = stats.participationPercentage + '%';
        
        //update summary
        document.getElementById('totalStudents').textContent = stats.total;
        document.getElementById('avgAbsences').textContent = stats.avgAbsences;
        document.getElementById('avgParticipation').textContent = stats.avgParticipation;
    }
    
    //initial update
    updateStats();
    
    window.addEventListener('storage', function(e) {
        if (e.key === 'attendanceData') {
            updateStats();
        }
    });
    
    setInterval(function() {
        updateStats();
    }, 1000);
});

