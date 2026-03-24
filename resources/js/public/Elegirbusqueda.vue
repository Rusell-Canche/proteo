<template>
    <div class="container">
      <h1>Elige la opción para buscar que necesites</h1>
      <br>
      <br>
      <div class="cards-container">
        <div class="card">
          <a class="nav-link btn-custom" href="/busquedaavanzada">
            <div class="nav-icon">
              <i class="fas fa-search"></i>
            </div>
            Búsqueda especifica
          </a>
        </div>
        <div class="card">
          <a class="nav-link btn-custom" href="/busquedaselect">
            <div class="nav-icon">
              <i class="fas fa-search-plus"></i>
            </div>
            Búsqueda normal
          </a>
        </div>
      </div>
    </div>
  </template>
  
  <style scoped>
  .container {
    text-align: center;
    padding: 40px 20px;
  }
  
  h1 {
    margin-bottom: 40px;
  }
  
  .cards-container {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
  }
  
  .card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
    text-align: center;
    width: 250px;
  }
  
  .card:hover {
    transform: scale(1.1);
  }
  
  .nav-link {
    text-decoration: none;
    color: black;
    font-weight: bold;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }

  .nav-link:hover .nav-icon  {
  color: #ac123f; /* Cambia el color del ícono al pasar el ratón sobre el enlace */
}
  
  .nav-icon i {
    font-size: 24px;
  }
  </style>
  