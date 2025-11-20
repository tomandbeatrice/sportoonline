<li class="nav-item has-treeview">
  <a href="#" class="nav-link">
    <i class="nav-icon fas fa-file-export"></i>
    <p>
      Export Yönetimi
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="{{ route('invoices.exports') }}" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Geçmiş</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('invoices.logs') }}" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Log Paneli</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('invoices.exportZip') }}" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Toplu Export</p>
      </a>
    </li>
  </ul>
</li>