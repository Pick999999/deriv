async function fetchData(url,Data) {
  try {
    

    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(Data)
    });

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    return await response.json();
  } catch (error) {
    console.error('Error in fetchData:', error);
    throw error;
  }

/* วิธีเรียกใช้ 
async function main() {
  const url = 'testAjax.php';
  const userData = {
      name: 'John Doe',
      email: 'john@example.com',
      age: 30
  };
  
  try {
    const data = await fetchData(url,userData);
    processB(data);
  } catch (error) {
    console.error('Error in main:', error);
  }
}

*/

} // end fetchData
