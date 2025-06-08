const errorMesgEl = document.querySelector('.error_message');
const budgetInputEl = document.querySelector('.budget_input');
const expensesDescEl = document.querySelector('.expenses_input');
const expensesAmountEl = document.querySelector('.expenses_amount');
const tblRecordsEl = document.querySelector('.tbl_data');
const cardsContainerEl = document.querySelector('.cards');
const budgetCardEl = document.querySelector('.budget_card');
const expensesCardEl = document.querySelector('.expenses_card');
const balancesCardEl = document.querySelector('.balance_card');
const successNotificationModal = document.getElementById('success-notification-modal'); // Define the success modal

let currentBudget = 0; // Initialize current budget
let currentExpenses = 0; // Initialize current expenses
let currentExpId = null; // Store the current expense ID for deletion
let currentSelectedCategory = '';
let isLoadingCategories = false;

function updateBalance() {
    const balance = currentBudget - currentExpenses;
    balancesCardEl.textContent = `₱ ${balance.toFixed(2)}`;
}

function updateExpensesCard(expenseList) {
    currentExpenses = 0;
    expenseList.forEach(exp => {
        currentExpenses += parseFloat(exp.exp_price);
    });
    expensesCardEl.textContent = currentExpenses.toFixed(2);
    updateBalance();
}

function fetchExpenses() {
    fetch('php/get_expenses.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                tblRecordsEl.innerHTML = `
                    <h3>Expense Table</h3>
                    ${data.data.map((exp, index) => `
                        <ul class="expense-row" data-id="${exp.exp_id}" style="
                            display: grid; 
                            grid-template-columns: 40px 1.2fr 2fr 1fr 1fr 100px; 
                            list-style: none; 
                            background-color: rgb(244,244,244); 
                            color: rgb(64,100,100); 
                            padding: 0.3rem 0.5rem; 
                            border-radius: 10px; 
                            margin: 0.5rem 0; 
                            cursor: pointer; 
                            font-size: 16px; 
                            align-items: center; 
                            gap: 10px; 
                            overflow: hidden;">
                            <li style="padding: 5px; font-weight: bold;">${index + 1}</li>
                            <li class="category-name" style="padding: 5px; font-weight: bold;">${exp.CategoryName}</li>
                            <li class="exp-name" style="padding: 5px; font-weight: bold;">${exp.exp_name}</li>
                            <li class="exp-price" style="padding: 5px; font-weight: bold;">₱ ${parseFloat(exp.exp_price).toFixed(2)}</li>
                            <li class="exp-date" style="padding: 5px; font-weight: bold;">${exp.Date}</li>
                            <li style="padding: 5px;">
                                <button class="edit-btn">Edit</button>
                                
                            </li>
                        </ul>
                    `).join('')}
                `;

                document.querySelectorAll('.edit-btn').forEach(button => {
                    button.addEventListener('click', handleEditClick);
                });
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', handleDeleteClick);
                });
function handleDeleteClick(e) {
    const btn = e.target;
    const expenseId = btn.getAttribute('data-exp-id'); // Get the expense ID
    // Show confirmation dialog
    if (confirm("Are you sure you want to delete this expense?")) {
        fetch('php/delete_expense.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ exp_id: expenseId }) // Send the expense ID
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchExpenses(); // Refresh the expense list
            } else {
                alert(data.error || 'Failed to delete expense.');
            }
        })
        .catch(err => {
            console.error('Error deleting expense:', err);
            alert('Error deleting expense.');
        });
    }
}
                updateExpensesCard(data.data);
            } else {
                expensesCardEl.textContent = '0.00';
                currentExpenses = 0;
                updateBalance();
            }
        })
        .catch(err => console.error('Fetch error:', err));
}



  function handleEditClick(e) {
      const btn = e.target;
      const row = btn.closest('.expense-row');

      const expNameEl = row.querySelector('.exp-name');
      const expPriceEl = row.querySelector('.exp-price');

      const currentName = expNameEl.textContent;
      const currentPrice = expPriceEl.textContent.replace(/[₱,\s]/g, '');

      expNameEl.innerHTML = `<input type="text" class="edit-exp-name" value="${currentName}" style="width: 100%;">`;
      expPriceEl.innerHTML = `<input type="number" class="edit-exp-price" value="${currentPrice}" style="width: 100%;">`;

      const actionCell = btn.parentElement;
      actionCell.innerHTML = `
          <button class="save-btn" style="font-size: 15px; margin-left:28px; background-color: #f4f4f4 ">Save</button>
          <button class="cancel-btn" style="margin-left:1px; color:red; height:10px; font-size:15px; background-color: #f4f4f4 ">Cancel</button>
      `;

      actionCell.querySelector('.save-btn').addEventListener('click', () => saveEdit(row));
      actionCell.querySelector('.cancel-btn').addEventListener('click', () => cancelEdit(row, currentName, currentPrice));
  }

  function saveEdit(row) {
      const id = row.dataset.id;
      const newName = row.querySelector('.edit-exp-name').value.trim();
      const newPrice = row.querySelector('.edit-exp-price').value.trim();

      if (!newName || !newPrice || isNaN(newPrice) || parseFloat(newPrice) < 0) {
          alert('Please enter valid name and price.');
          return;
      }

      fetch('php/update_expense.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ exp_id: id, exp_name: newName, exp_price: newPrice })
      })
      .then(res => res.json())
      .then(data => {
          if (data.success) {
              fetchExpenses();  // reload updated list and totals
          } else {
              alert(data.error || 'Failed to update expense.');
          }
      })
      .catch(err => {
          console.error('Error updating expense:', err);
          alert('Error updating expense.');
      });
  }

  function cancelEdit(row, originalName, originalPrice) {
      // Restore the original name and price
      row.querySelector('.exp-name').innerHTML = originalName;
      row.querySelector('.exp-price').innerHTML = `₱ ${parseFloat(originalPrice).toFixed(2)}`;

      // Restore the action buttons
      const actionCell = row.querySelector('li:last-child');
      actionCell.innerHTML = `
          <button class="edit-btn">Edit</button>
          <button class="delete-btn">Delete</button>
      `;
      // Reattach the event listener for the edit and delete buttons
      actionCell.querySelector('.edit-btn').addEventListener('click', handleEditClick);
      actionCell.querySelector('.delete-btn').addEventListener('click', handleDeleteClick);
  }

  function loadBudget() {
      fetch('php/get_budget.php?' + new Date().getTime()) // Prevent caching
          .then(res => {
              if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
              return res.json();
          })
          .then(data => {
              console.log('Budget loaded:', data);
              const budgetCard = document.querySelector('.budget_card');
              if (data.success) {
                  currentBudget = parseFloat(data.budget); // Update current budget
                  budgetCard.textContent = `${currentBudget.toFixed(2)}`; // Update the budget display
                  updateBalance(); // Update balance after loading budget
              } else {
                  budgetCard.textContent = '$0';
                  currentBudget = 0; // Reset current budget if no budget is available
                  updateBalance(); // Update balance if no budget
              }
          })
          .catch(err => {
              console.error('Failed to load budget:', err);
          });
  }

  function addBudget(e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      
      fetch('php/add_budget.php', {
          method: 'POST',
          body: formData
      })
      .then(res => res.json())
      .then(data => {
          console.log('Add budget response:', data);
          if (data.success) {
              loadBudget();     // Refresh budget card
              fetchExpenses();  // Refresh expenses and balance
              e.target.reset();
          } else {
              alert(data.error || 'Failed to update budget');
          }
      })
      .catch(err => console.error('Error:', err));
  }

  function addExpense(e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      fetch('php/add_expense.php', {
          method: 'POST',
          body: formData
      })
      .then(res => res.json())
      .then(data => {
          if (data.success) {
              e.target.reset();
              fetchExpenses(); // refresh expense list and totals
          } else {
              alert(data.error || 'Failed to add expense');
          }
      });
  }

document.addEventListener('DOMContentLoaded', () => {
    loadBudget();
    fetchExpenses();

    const monthPicker = document.getElementById('monthPicker');
    const categorySelector = document.getElementById('categorySelector');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');
    const initialMonth = monthPicker.value;

    // Initial load
    fetchMonthlySummary(initialMonth);
    fetchExpenseBreakdown(initialMonth);
    fetchSavingGoals(initialMonth);
    loadCategories(initialMonth);

    document.getElementById('budgetForm').addEventListener('submit', addBudget);
    document.getElementById('expensesForm').addEventListener('submit', addExpense);

    // Month picker change event
    monthPicker.addEventListener('change', () => {
    const selectedMonth = monthPicker.value;
    currentSelectedCategory = ''; // Reset category when month changes
    categorySelector.value = '';
    
    fetchMonthlySummary(selectedMonth);
    fetchExpenseBreakdown(selectedMonth);
    clearSavingGoalsData();
    fetchSavingGoals(selectedMonth);
    loadCategories(selectedMonth);
    updateReportView();
    updateDownloadLink(); // Add this line
});
     // Category selector change event
    categorySelector.addEventListener('change', () => {
    const selectedMonth = monthPicker.value;
    const selectedCategory = categorySelector.value;
    currentSelectedCategory = selectedCategory;
    
    if (selectedCategory) {
        // Show category-specific data
        fetchCategoryExpenses(selectedMonth, selectedCategory);
        hideSavingGoalsSection();
        updateReportView(categorySelector.options[categorySelector.selectedIndex].text);
    } else {
        // Show overall report
        fetchMonthlySummary(selectedMonth);
        fetchExpenseBreakdown(selectedMonth);
        fetchSavingGoals(selectedMonth);
        showSavingGoalsSection();
        updateReportView();
    }
    updateDownloadLink(); // Add this line
});

    // Reset filters button
    resetFiltersBtn.addEventListener('click', () => {
    const selectedMonth = monthPicker.value;
    currentSelectedCategory = '';
    categorySelector.value = '';
    
    fetchMonthlySummary(selectedMonth);
    fetchExpenseBreakdown(selectedMonth);
    fetchSavingGoals(selectedMonth);
    showSavingGoalsSection();
    updateReportView();
    updateDownloadLink(); // Add this line
});
});

// Function to load available categories for the selected month
function loadCategories(selectedMonth) {
    if (isLoadingCategories) return;
    isLoadingCategories = true;
    
    fetch(`php/get_categories.php?month=${selectedMonth}`)
        .then(res => res.json())
        .then(data => {
            const categorySelector = document.getElementById('categorySelector');
            
            // Clear existing options except the first one
            categorySelector.innerHTML = '<option value="">All Categories (Overall Report)</option>';
            
            if (data.success && data.categories.length > 0) {
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.categoryId;
                    option.textContent = category.categoryName;
                    categorySelector.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        })
        .finally(() => {
            isLoadingCategories = false;
        });
}

// Function to fetch expenses for a specific category
function fetchCategoryExpenses(selectedMonth, categoryId) {
    fetch(`php/get_category_expenses.php?month=${selectedMonth}&category=${categoryId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateCategoryView(data);
            } else {
                console.error('Error fetching category expenses:', data.error);
                showNoCategoryData();
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showNoCategoryData();
        });
}

// Function to update the view when showing category-specific data
function updateCategoryView(data) {
    // Update the expense breakdown title and table
    const expenseBreakdownTitle = document.getElementById('expenseBreakdownTitle');
    expenseBreakdownTitle.textContent = `${data.category_name} Expenses`;
    
    // Update total expenses display
    const totalDisplay = document.querySelector(".total-expenses-display");
    totalDisplay.textContent = `Total ${data.category_name} Expenses: ₱${parseFloat(data.total_category_expenses).toLocaleString()} (${data.budget_percentage}% of Budget)`;
    
    // Update expense table headers for category view
    const tableHeader = document.getElementById('expenseTableHeader');
    tableHeader.innerHTML = `
        <th>Expense Name</th>
        <th>Amount</th>
        <th>Date</th>
    `;
    
    // Update table body with category expenses
    const tbody = document.querySelector(".summary-table tbody");
    tbody.innerHTML = "";
    
    if (data.expenses && data.expenses.length > 0) {
        data.expenses.forEach(expense => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${expense.exp_name}</td>
                <td>₱${parseFloat(expense.exp_price).toLocaleString()}</td>
                <td>${expense.expense_date}</td>
                
            `;
            tbody.appendChild(tr);
        });
    } else {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color: #888;">No expenses found for this category in the selected month.</td></tr>`;
    }
    
    // Hide pie chart for category view
    const pieChart = document.getElementById('expensePieChart');
    pieChart.style.display = 'none';
    
    // Update metrics for category view
    updateCategoryMetrics(data);
    
    // Clear existing pie chart
    if (expensePieChart) {
        expensePieChart.destroy();
        expensePieChart = null;
    }
}

// Function to update metrics for category view
function updateCategoryMetrics(data) {
    // Update the expenses label and value
    const expensesLabel = document.getElementById('expensesLabel');
    const expensesValue = document.querySelector('.expenses-value');
    
    expensesLabel.textContent = `${data.category_name} Expenses`;
    expensesValue.textContent = `₱ ${parseFloat(data.total_category_expenses).toLocaleString()}`;
    
    // For category view, we'll show different insights
    generateCategoryInsights(data);
}

// Function to generate insights for category view
function generateCategoryInsights(data) {
    const insightsList = document.querySelector('.insight-list');
    insightsList.innerHTML = '';
    
    const budgetPercentage = parseFloat(data.budget_percentage);
    const categoryName = data.category_name;
    const totalExpenses = parseFloat(data.total_category_expenses);
    
    if (budgetPercentage > 50) {
        insightsList.innerHTML += `<li>Your ${categoryName} expenses are consuming ${budgetPercentage.toFixed(2)}% of your total budget. Consider reducing spending in this category.</li>`;
    } else if (budgetPercentage > 30) {
        insightsList.innerHTML += `<li>Your ${categoryName} expenses represent ${budgetPercentage.toFixed(2)}% of your budget. Monitor this category to ensure it stays within reasonable limits.</li>`;
    } else {
        insightsList.innerHTML += `<li>Your ${categoryName} expenses are well controlled at ${budgetPercentage.toFixed(2)}% of your budget. Good job managing this category!</li>`;
    }
    
    if (data.expenses && data.expenses.length > 0) {
        const averageExpense = totalExpenses / data.expenses.length;
        insightsList.innerHTML += `<li>You made ${data.expenses.length} ${categoryName} transaction(s) this month with an average of ₱${averageExpense.toFixed(2)} per transaction.</li>`;
        
        // Find highest expense
        const highestExpense = Math.max(...data.expenses.map(exp => parseFloat(exp.exp_price)));
        const highestExpenseItem = data.expenses.find(exp => parseFloat(exp.exp_price) === highestExpense);
        insightsList.innerHTML += `<li>Your highest ${categoryName} expense was ₱${highestExpense.toFixed(2)} for "${highestExpenseItem.exp_name}".</li>`;
    }
}

// Function to show no data message for category
function showNoCategoryData() {
    const tbody = document.querySelector(".summary-table tbody");
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color: #888;">No data found for this category in the selected month.</td></tr>`;
    
    const totalDisplay = document.querySelector(".total-expenses-display");
    totalDisplay.textContent = "No expenses found for this category.";
    
    // Hide pie chart
    const pieChart = document.getElementById('expensePieChart');
    pieChart.style.display = 'none';
}

// Function to hide saving goals section
function hideSavingGoalsSection() {
    const savingGoalsSection = document.getElementById('savingGoals');
    savingGoalsSection.style.display = 'none';
}

// Function to show saving goals section
function showSavingGoalsSection() {
    const savingGoalsSection = document.getElementById('savingGoals');
    savingGoalsSection.style.display = 'block';
}

// Function to update report view indicator
function updateReportView(categoryName = null) {
    const reportTypeIndicator = document.getElementById('reportTypeIndicator');
    const reportTypeText = document.getElementById('reportTypeText');
    const pieChart = document.getElementById('expensePieChart');
    const expenseBreakdownTitle = document.getElementById('expenseBreakdownTitle');
    const tableHeader = document.getElementById('expenseTableHeader');
    const expensesLabel = document.getElementById('expensesLabel');
    
    if (categoryName) {
        // Category view
        reportTypeIndicator.style.display = 'block';
        reportTypeText.textContent = `${categoryName} Category Report`;
        pieChart.style.display = 'none';
    } else {
        // Overall view
        reportTypeIndicator.style.display = 'none';
        reportTypeText.textContent = 'Overall Report';
        pieChart.style.display = 'block';
        expenseBreakdownTitle.textContent = 'Expense Breakdown';
        expensesLabel.textContent = 'Total Expenses';
        
        // Restore original table headers
        tableHeader.innerHTML = `
            <th>Category</th>
            <th>Amount Spent</th>
            <th>% of Total Expenses</th>
            <th>Date</th>
        `;
    }
}
function getDefaultMonth() {
    const monthPicker = document.getElementById('monthPicker');
    return monthPicker ? monthPicker.value : '';
}
function updateDownloadLink() {
    const downloadBtn = document.querySelector('.download-report-btn');
    const monthPicker = document.getElementById('monthPicker');
    const categorySelector = document.getElementById('categorySelector');
    
    if (!downloadBtn) return;
    
    const selectedMonth = monthPicker.value;
    const selectedCategory = categorySelector.value;
    
    let downloadUrl = `php/generate_report.php?month=${selectedMonth}`;
    
    if (selectedCategory) {
        downloadUrl += `&category=${selectedCategory}`;
        // Update button text for category-specific download
        const categoryName = categorySelector.options[categorySelector.selectedIndex].text;
        downloadBtn.querySelector('span').textContent = `Download ${categoryName} Category Report`;
    } else {
        // Reset to default text for overall report
        downloadBtn.querySelector('span').textContent = 'Download Monthly Summary Report';
    }
    
    downloadBtn.href = downloadUrl;
}
function fetchMonthlySummary(selectedMonth) {
    fetch(`php/get_monthly_summary.php?month=${selectedMonth}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelector('.budget-value').textContent = ` ${data.budget}`;
                document.querySelector('.expenses-value').textContent = ` ${data.total_expenses}`;
                document.querySelector('.balance-value').textContent = ` ${data.balance}`;
                document.querySelector('.savings-rate-value').textContent = `${data.savings_rate}`;
                generateInsights(data);
            } else {
                console.error(data.error);
            }
        })
        .catch(err => console.error('Fetch error:', err));
}

function generateInsights(data) {
    const insightsList = document.querySelector('.insight-list');
    insightsList.innerHTML = ''; // Clear existing insights

    // Budget vs expenses insight
    const totalExpenses = parseFloat(data.total_expenses) || 0;
    const budget = parseFloat(data.budget) || 0;
    if (totalExpenses > budget) {
        insightsList.innerHTML += `<li>You have exceeded your budget by ₱${(totalExpenses - budget).toFixed(2)}. Review your spending categories.</li>`;
    } else {
        insightsList.innerHTML += `<li>You are within your budget. Great job managing your expenses!</li>`;
    }

    // Savings rate insight
    const savingsRate = parseFloat(data.savings_rate) || 0;
    if (savingsRate <= 40) {
        insightsList.innerHTML += `<li>Your overall savings rate is below 40%. Consider cutting discretionary spending to increase your savings rate.</li>`;
    } else if (savingsRate <= 70) {
        insightsList.innerHTML += `<li>Your savings rate is good. Keep up the consistent saving habit!</li>`;
    } else {
        insightsList.innerHTML += `<li>Excellent savings rate! You're doing very well with managing your finances.</li>`;
    }

    // Saving goals insights
    if (Array.isArray(data.savingGoals) && data.savingGoals.length > 0) {
        data.savingGoals.forEach(goal => {
            const progress = parseFloat(goal.progressPercent) || 0;
            let message = '';

            if (progress <= 25) {
                message = `Your saving goal "${goal.goalName}" is very low at ${progress.toFixed(2)}%. You need to increase your saving or reduce expenses to reach your target.`;
            } else if (progress <= 30) {
                message = `Your saving goal "${goal.goalName}" is at ${progress.toFixed(2)}%. Try to boost your savings a bit more to stay on track.`;
            } else if (progress <= 40) {
                message = `Your saving goal "${goal.goalName}" progress is ${progress.toFixed(2)}%. Keep going and maintain your saving habit!`;
            } else if (progress <= 50) {
                message = `You're halfway there on your saving goal "${goal.goalName}" with ${progress.toFixed(2)}% progress. Great effort!`;
            } else if (progress >= 75 && progress < 90) {
                message = `You're doing great on your saving goal "${goal.goalName}" with ${progress.toFixed(2)}% progress. Keep it up!`;
            } else if (progress >= 90) {
                message = `Excellent! You have almost reached or fully met your saving goal "${goal.goalName}" with ${progress.toFixed(2)}% progress.`;
            }

            if (message) {
                insightsList.innerHTML += `<li>${message}</li>`;
            }
        });
    } else {
        insightsList.innerHTML += `<li>You have no active saving goals. Consider setting some to boost your financial health.</li>`;
    }
}

// Global variables
let expensePieChart = null;
let savingGoalsPieChart = null;

// Function to get current month in YYYY-MM format
function getCurrentMonth() {
    const currentDate = new Date();
    return currentDate.getFullYear() + '-' + String(currentDate.getMonth() + 1).padStart(2, '0');
}

// Main function to load monthly report - call this when sidebar is clicked
function loadMonthlyReport() {
    console.log('Loading monthly report...');
    
    // Get current month
    const currentMonth = getCurrentMonth();
    
    // Set month selector if it exists
    const monthSelector = document.querySelector('#monthSelector, [name="month"], input[type="month"]');
    if (monthSelector) {
        monthSelector.value = currentMonth;
    }
    
    // Load data for current month
    fetchExpenseBreakdown(currentMonth);
    fetchSavingGoals(currentMonth);
}

function fetchExpenseBreakdown(selectedMonth) {
    // If a category is selected, don't run this function
    if (currentSelectedCategory) {
        return;
    }
    
    console.log('Fetching expense breakdown for:', selectedMonth);
    
    fetch(`php/get_expense_breakdown.php?month=${selectedMonth}`)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            console.log('Expense data received:', data);
            
            if (data.success) {
                const tbody = document.querySelector(".summary-table tbody");
                if (!tbody) {
                    console.error('Expense table body not found');
                    return;
                }
                
                tbody.innerHTML = "";

                const totalExpenses = data.total_expenses || 0;
                const totalDisplay = document.querySelector(".total-expenses-display");
                if (totalDisplay) {
                    totalDisplay.textContent = `Total Expenses: ₱${totalExpenses.toLocaleString()}`;
                }

                const categories = [];
                const amounts = [];
                const colors = [];

                const pastelColors = [
                    "#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0",
                    "#9966FF", "#dec3b3", "#66FF66", "#FF6666",
                    "#a8abde", "#FFA07A", "#BA55D3", "#7FFFD4",
                    "#FFD700", "#40E0D0"
                ];

                let colorIndex = 0;

                if (data.data && data.data.length > 0) {
                    data.data.forEach(expense => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>${expense.category}</td>
                            <td>₱${parseFloat(expense.amount_spent).toLocaleString()}</td>
                            <td>${parseFloat(expense.percentage).toFixed(2)}%</td>
                            <td>${expense.date}</td>
                        `;
                        tbody.appendChild(tr);

                        categories.push(expense.category);
                        amounts.push(parseFloat(expense.amount_spent));

                        if (colorIndex < pastelColors.length) {
                            colors.push(pastelColors[colorIndex]);
                        } else {
                            colors.push(getRandomPastel());
                        }
                        colorIndex++;
                    });

                    // Show pie chart and render it
                    const pieChart = document.getElementById('expensePieChart');
                    pieChart.style.display = 'block';
                    renderExpensePieChart(categories, amounts, colors);
                } else {
                    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color: #888;">No expenses found for this month.</td></tr>`;
                    // Clear pie chart if no data
                    if (expensePieChart) {
                        expensePieChart.destroy();
                        expensePieChart = null;
                    }
                }
            } else {
                console.error("Error fetching expense breakdown data:", data.error);
                const totalDisplay = document.querySelector(".total-expenses-display");
                if (totalDisplay) {
                    totalDisplay.textContent = "No expense data available for this month.";
                }
                
                const tbody = document.querySelector(".summary-table tbody");
                if (tbody) {
                    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color: #888;">No expenses found for this month.</td></tr>`;
                }
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            const totalDisplay = document.querySelector(".total-expenses-display");
            if (totalDisplay) {
                totalDisplay.textContent = "Failed to load expenses.";
            }
            
            const tbody = document.querySelector(".summary-table tbody");
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color: red;">Failed to load expense data.</td></tr>`;
            }
        });
}

function renderExpensePieChart(categories, amounts, colors) {
    const canvas = document.getElementById("expensePieChart");
    if (!canvas) {
        console.error('Expense pie chart canvas not found');
        return;
    }
    
    const ctx = canvas.getContext("2d");
    canvas.style.width = "320px";
    canvas.style.height = "320px";
    canvas.width = 320;
    canvas.height = 320;
    
    if (expensePieChart) {
        expensePieChart.destroy();
    }

    expensePieChart = new Chart(ctx, {
        type: "pie",
        data: {
            labels: categories,
            datasets: [{
                data: amounts,
                backgroundColor: colors,
                borderColor: "#fff",
                borderWidth: 2,
                hoverOffset: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        font: { size: 14, weight: "bold" },
                        color: "#333"
                    }
                },
                title: {
                    display: true,
                    text: "Expense Breakdown by Category",
                    font: { size: 18, weight: "bold" },
                    color: "#222"
                }
            }
        }
    });
}

function getRandomPastel() {
    const hue = Math.floor(Math.random() * 360);
    return `hsl(${hue}, 70%, 80%)`;
}

function clearSavingGoalsData() {
    const tbody = document.getElementById('goals-table-body');
    if (tbody) {
        tbody.innerHTML = '';
    }

    if (savingGoalsPieChart) {
        savingGoalsPieChart.destroy();
        savingGoalsPieChart = null;
    }

    const canvas = document.getElementById("savingGoalsPieChart");
    if (canvas) {
        const ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
}

function fetchSavingGoals(selectedMonth) {
    console.log("Fetching saving goals for month:", selectedMonth);
    
    fetch(`php/fetch_goals.php?month=${encodeURIComponent(selectedMonth)}`)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            console.log("Fetched goals data:", data);
            
            const tbody = document.getElementById('goals-table-body');
            if (!tbody) {
                console.error('Goals table body not found');
                return;
            }
            
            tbody.innerHTML = '';

            if (data.status === 'success' && Array.isArray(data.goals) && data.goals.length > 0) {
                const goalNames = [];
                const currentAmounts = [];
                const colors = [];
                const pastelColors = [
                    "#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF",
                    "#FF9F40", "#66FF66", "#FF6666", "#66B3FF", "#FFA07A",
                    "#BA55D3", "#7FFFD4", "#FFD700", "#40E0D0"
                ];

                data.goals.forEach((goal, index) => {
                    const variance = goal.targetAmount - goal.currentAmount;
                    const progressPercentage = goal.targetAmount > 0 ? (goal.currentAmount / goal.targetAmount) * 100 : 0;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${goal.goalName}</td>
                        <td>₱${parseFloat(goal.targetAmount).toLocaleString()}</td>
                        <td>₱${parseFloat(goal.currentAmount).toLocaleString()}</td>
                        <td>₱${parseFloat(variance).toLocaleString()}</td>
                        <td>${progressPercentage.toFixed(2)}% ${progressPercentage >= 100 ? '<span style="color: green;">(Achieved!)</span>' : ''}</td>
                    `;
                    tbody.appendChild(tr);

                    goalNames.push(goal.goalName);
                    currentAmounts.push(parseFloat(goal.currentAmount));
                    colors.push(pastelColors[index % pastelColors.length]);
                });

                renderSavingGoalsPieChart(goalNames, currentAmounts, colors);
            } else {
                console.log("No goals found for this month.");
                tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; color: #888;">No saving goals found for this month.</td></tr>`;
                
                if (savingGoalsPieChart) {
                    savingGoalsPieChart.destroy();
                    savingGoalsPieChart = null;
                }

                const canvas = document.getElementById("savingGoalsPieChart");
                if (canvas) {
                    const ctx = canvas.getContext("2d");
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                }
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            const tbody = document.getElementById('goals-table-body');
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; color: red;">Failed to load saving goals.</td></tr>`;
            }
        });
}

function renderSavingGoalsPieChart(goalNames, currentAmounts, colors) {
    const canvas = document.getElementById("savingGoalsPieChart");
    if (!canvas) {
        console.error('Saving goals pie chart canvas not found');
        return;
    }
    
    console.log("Rendering pie chart with:", goalNames, currentAmounts, colors);
    
    canvas.style.width = "320px";
    canvas.style.height = "320px";
    canvas.width = 320;
    canvas.height = 320;

    const ctx = canvas.getContext("2d");

    const allZero = currentAmounts.every(amount => isNaN(amount) || amount <= 0);
    const adjustedAmounts = allZero ? currentAmounts.map(() => 1) : currentAmounts;

    if (savingGoalsPieChart) {
        savingGoalsPieChart.destroy();
    }

    savingGoalsPieChart = new Chart(ctx, {
        type: "pie",
        data: {
            labels: goalNames,
            datasets: [{
                data: adjustedAmounts,
                backgroundColor: colors,
                borderColor: "#fff",
                borderWidth: 2,
                hoverOffset: 30
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        font: { size: 14, weight: "bold" },
                        color: "#333"
                    }
                },
                title: {
                    display: true,
                    text: "Saving Goals Progress",
                    font: { size: 18, weight: "bold" },
                    color: "#222"
                }
            }
        }
    });
}

// Function to handle month selector changes
function handleMonthChange(selectedMonth) {
    console.log('Month changed to:', selectedMonth);
    fetchExpenseBreakdown(selectedMonth);
    fetchSavingGoals(selectedMonth);
}

function errorMessage(message) {
    const errorMesgEl = document.querySelector('.error-message, #errorMessage');
    if (errorMesgEl) {
        errorMesgEl.innerHTML = `<p>${message}</p>`;
        errorMesgEl.classList.add("error");
        setTimeout(() => {
            errorMesgEl.classList.remove("error");
        }, 2500);
    }
    console.error(message);
}

let editingGoalId = null;

// Modal references
const deleteConfirmationModal = document.getElementById('delete-confirmation-modal');
const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
const goalAchievementModal = document.getElementById('goal-achievement-modal');
const achievementOkBtn = document.getElementById('achievement-ok-btn');


let goalIdToDelete = null;

// Form references
const savingGoalsForm = document.getElementById('saving-goals-form');
const savedGoalsContainer = document.querySelector('.saved-goals-list');
const cancelButton = document.querySelector('.cancel-btn');
const targetAmountInput = document.getElementById('target-amount');
const savedAmountInput = document.getElementById('saved-amount');
const savedAmountMessage = document.getElementById('saved-amount-message');

// Enhanced validation for saved amount
function validateSavedAmount() {
    const targetAmount = parseFloat(targetAmountInput.value) || 0;
    const savedAmount = parseFloat(savedAmountInput.value) || 0;
    
    savedAmountMessage.textContent = '';
    savedAmountMessage.className = 'validation-message';
    
    if (savedAmount < 0) {
        savedAmountMessage.textContent = 'Amount saved cannot be negative.';
        savedAmountMessage.classList.add('error');
        return false;
    }
    
    // Show warning when amount exceeds target
    if (targetAmount > 0 && savedAmount > targetAmount) {
        savedAmountMessage.textContent = `Amount exceeds target. Only ₱${targetAmount.toLocaleString()} will be saved.`;
        savedAmountMessage.classList.add('warning');
        return true;
    }
    
    // Show success message when target is reached
    if (targetAmount > 0 && savedAmount === targetAmount) {
        savedAmountMessage.textContent = 'Congratulations! You\'ve reached your goal! 🎉';
        savedAmountMessage.classList.add('success');
        return true;
    }
    
    return true;
}

// Add event listeners for real-time validation
targetAmountInput.addEventListener('input', validateSavedAmount);
savedAmountInput.addEventListener('input', validateSavedAmount);

// Function to fetch and display saved goals
function fetchSavedGoals() {
    fetch('php/fetch_goals.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                savedGoalsContainer.innerHTML = '';

                data.goals.forEach(goal => {
                    const savedGoalCardElement = document.createElement('div');
                    savedGoalCardElement.classList.add('saved-goal-card');
                    
                    const targetAmount = parseFloat(goal.targetAmount);
                    const currentAmount = parseFloat(goal.currentAmount); // This is now capped from PHP
                    const rawPercentage = (currentAmount / targetAmount) * 100;
                    const progressPercentage = Math.min(100, Math.round(rawPercentage));
                    const isCompleted = currentAmount >= targetAmount;
                    
                    if (isCompleted) {
                        savedGoalCardElement.classList.add('goal-completed');
                    }

                    const goalNameElement = document.createElement('h4');
                    goalNameElement.classList.add('goal-name');
                    goalNameElement.textContent = goal.goalName;

                    const targetAmountElement = document.createElement('p');
                    targetAmountElement.classList.add('target-amount');
                    targetAmountElement.textContent = `Target: ₱${targetAmount.toLocaleString()}`;

                    const savedAmountElement = document.createElement('p');
                    savedAmountElement.classList.add('saved-amount');
                    savedAmountElement.textContent = `Saved: ₱${currentAmount.toLocaleString()}`;

                    savedGoalCardElement.appendChild(goalNameElement);
                    savedGoalCardElement.appendChild(targetAmountElement);
                    savedGoalCardElement.appendChild(savedAmountElement);

                    // Add goal status if completed
                    if (isCompleted) {
                        const goalStatusElement = document.createElement('p');
                        goalStatusElement.classList.add('goal-status');
                        goalStatusElement.textContent = '🎉 Goal Achieved!';
                        savedGoalCardElement.appendChild(goalStatusElement);
                    }

                    const progressContainerElement = document.createElement('div');
                    progressContainerElement.classList.add('progress-container');

                    const progressTextAboveElement = document.createElement('span');
                    progressTextAboveElement.classList.add('progress-text-above');
                    progressTextAboveElement.textContent = `${progressPercentage}%`;

                    const progressBarContainerElement = document.createElement('div');
                    progressBarContainerElement.classList.add('progress-bar-container');

                    const progressBarElement = document.createElement('div');
                    progressBarElement.classList.add('progress-bar');
                    if (isCompleted) {
                        progressBarElement.classList.add('completed');
                    }
                    progressBarElement.style.width = `${progressPercentage}%`;

                    progressBarContainerElement.appendChild(progressBarElement);
                    progressContainerElement.appendChild(progressTextAboveElement);
                    progressContainerElement.appendChild(progressBarContainerElement);

                    const editButton = document.createElement('button');
                    editButton.textContent = 'Edit';
                    editButton.classList.add('edit-goal-btn');
                    editButton.addEventListener('click', () => editGoal(goal.goalID));

                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Delete';
                    deleteButton.classList.add('delete-goal-btn');
                    deleteButton.addEventListener('click', () => deleteGoal(goal.goalID));

                    const actionsContainer = document.createElement('div');
                    actionsContainer.classList.add('goal-actions');
                    actionsContainer.appendChild(editButton);
                    actionsContainer.appendChild(deleteButton);

                    savedGoalCardElement.appendChild(progressContainerElement);
                    savedGoalCardElement.appendChild(actionsContainer);

                    savedGoalsContainer.appendChild(savedGoalCardElement);
                });
            } else {
                savedGoalsContainer.innerHTML = '<p>No saved goals found.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching goals:', error);
            alert('An error occurred while fetching saved goals.');
        });
}

// Function to show goal achievement modal
function showGoalAchievementModal(goalName) {
    const achievementMessage = document.getElementById('achievement-message');
    achievementMessage.textContent = `Congratulations! You've achieved your "${goalName}" goal!`;
    goalAchievementModal.classList.remove('hidden');
}

// Function to save a goal (or edit an existing one)
function saveGoal(e) {
    e.preventDefault();

    const goalName = document.getElementById('goal-name').value.trim();
    const targetAmount = parseFloat(document.getElementById('target-amount').value);
    const savedAmount = parseFloat(document.getElementById('saved-amount').value);

    // Basic validation only
    if (!goalName) {
        alert('Please enter a goal name.');
        return;
    }

    if (isNaN(targetAmount) || targetAmount <= 0) {
        alert('Please enter a valid target amount greater than 0.');
        return;
    }

    if (!validateSavedAmount()) {
        return;
    }

    if (isNaN(savedAmount) || savedAmount < 0) {
        alert('Please enter a valid saved amount (0 or greater).');
        return;
    }

    // Check if goal is achieved (based on target amount, not input amount)
    const actualSavedAmount = Math.min(savedAmount, targetAmount);
    const isGoalAchieved = actualSavedAmount >= targetAmount;
    const isNewGoal = editingGoalId === null;

    const formData = new FormData();
    formData.append('goalName', goalName);
    formData.append('targetAmount', targetAmount);
    formData.append('currentAmount', savedAmount); // Send original amount, capping happens in PHP
    
    if (editingGoalId !== null) {
        formData.append('editingGoalId', editingGoalId);
    }

    fetch('php/save_goal.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = data.message;
            notification.classList.add('visible');
            setTimeout(() => notification.classList.remove('visible'), 3000);

            // Show achievement modal if goal is achieved and it's a new goal
            if (isGoalAchieved && isNewGoal) {
                showGoalAchievementModal(goalName);
            }

            // Reset the form
            savingGoalsForm.reset();
            cancelButton.style.display = 'none';
            document.querySelector('.save-goal-btn').textContent = 'Save Goal';
            editingGoalId = null;
            
            // Clear validation message
            savedAmountMessage.textContent = '';

            fetchSavedGoals();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the goal.');
    });
}

// Function to populate the form for editing a goal
function editGoal(goalID) {
    fetch(`php/get_goal.php?goalID=${goalID}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const goal = data.goal;
                document.getElementById('goal-name').value = goal.goalName;
                document.getElementById('target-amount').value = goal.targetAmount;
                document.getElementById('saved-amount').value = goal.currentAmount;
                editingGoalId = goalID;
                cancelButton.style.display = 'inline-block';
                document.querySelector('.save-goal-btn').textContent = 'Update Goal';
                
                // Trigger validation
                validateSavedAmount();
            } else {
                alert(data.message || 'Failed to load goal details');
            }
        })
        .catch(error => {
            console.error('Error fetching goal details:', error);
            alert('Error while fetching goal details');
        });
}

// Function to delete a goal
function deleteGoal(goalId) {
    goalIdToDelete = goalId;
    deleteConfirmationModal.classList.remove('hidden');
}

// Event listeners for modals
confirmDeleteBtn.addEventListener('click', () => {
    if (!goalIdToDelete) return;

    fetch('php/delete_goal.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ goalID: goalIdToDelete })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            fetchSavedGoals();
            successNotificationModal.classList.remove('hidden');
            setTimeout(() => {
                successNotificationModal.classList.add('hidden');
            }, 2000);
        } else {
            alert(data.message || 'Failed to delete goal');
        }
    })
    .catch(error => {
        console.error('Error deleting goal:', error);
        alert('Error while deleting the goal');
    })
    .finally(() => {
        deleteConfirmationModal.classList.add('hidden');
        goalIdToDelete = null;
    });
});

cancelDeleteBtn.addEventListener('click', () => {
    deleteConfirmationModal.classList.add('hidden');
    goalIdToDelete = null;
});

achievementOkBtn.addEventListener('click', () => {
    goalAchievementModal.classList.add('hidden');
});

// Close achievement modal when clicking outside
goalAchievementModal.addEventListener('click', (e) => {
    if (e.target === goalAchievementModal) {
        goalAchievementModal.classList.add('hidden');
    }
});

// Function to handle input changes
function handleInputChange() {
    cancelButton.style.display = 'inline-block';
    document.querySelector('.save-goal-btn').textContent = editingGoalId !== null ? 'Update Goal' : 'Save Goal';
}

// Function to clear the form
function clearForm() {
    savingGoalsForm.reset();
    cancelButton.style.display = 'none';
    editingGoalId = null;
    document.querySelector('.save-goal-btn').textContent = 'Save Goal';
    savedAmountMessage.textContent = '';
}

// Add event listeners
savingGoalsForm.addEventListener('submit', saveGoal);
savingGoalsForm.addEventListener('input', handleInputChange);
cancelButton.addEventListener('click', clearForm);

// Initial display of saved goals on page load
fetchSavedGoals();


