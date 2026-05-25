<?php

namespace App\Composite;

/**
 * Composite Pattern - Menu Component Interface
 */
interface MenuComponent
{
    public function render(): string;
    public function add(MenuComponent $component): void;
    public function remove(MenuComponent $component): void;
    public function getChildren(): array;
}

/**
 * Composite Pattern - Menu Item (Leaf)
 */
class MenuItem implements MenuComponent
{
    private string $label;
    private string $url;
    private array $attributes;

    public function __construct(string $label, string $url, array $attributes = [])
    {
        $this->label = $label;
        $this->url = $url;
        $this->attributes = $attributes;
    }

    public function render(): string
    {
        $attrs = '';
        foreach ($this->attributes as $key => $value) {
            $attrs .= " $key=\"$value\"";
        }
        return "<a href=\"{$this->url}\"{$attrs}>{$this->label}</a>";
    }

    public function add(MenuComponent $component): void
    {
        throw new \Exception("MenuItem'a component eklenemez");
    }

    public function remove(MenuComponent $component): void
    {
        throw new \Exception("MenuItem'dan component silinemez");
    }

    public function getChildren(): array
    {
        return [];
    }
}

/**
 * Composite Pattern - Menu (Composite)
 */
class Menu implements MenuComponent
{
    private string $label;
    private array $children = [];

    public function __construct(string $label = '')
    {
        $this->label = $label;
    }

    public function render(): string
    {
        $html = '';
        if (!empty($this->label)) {
            $html .= "<div class=\"menu-group\"><strong>{$this->label}</strong></div>";
        }
        $html .= '<nav class="nav-links">';
        foreach ($this->children as $child) {
            $html .= $child->render();
        }
        $html .= '</nav>';
        return $html;
    }

    public function add(MenuComponent $component): void
    {
        $this->children[] = $component;
    }

    public function remove(MenuComponent $component): void
    {
        $key = array_search($component, $this->children, true);
        if ($key !== false) {
            unset($this->children[$key]);
        }
    }

    public function getChildren(): array
    {
        return $this->children;
    }
}

/**
 * Menu Builder - Composite Pattern kullanarak menü oluşturur
 */
class MenuBuilder
{
    public static function buildUserMenu(): Menu
    {
        $menu = new Menu();
        $menu->add(new MenuItem('Biletlerim', '/my_tickets.php', ['class' => 'secondary']));
        $menu->add(new MenuItem('Çıkış Yap', '/logout.php', ['class' => 'button']));
        return $menu;
    }

    public static function buildCompanyMenu(): Menu
    {
        $menu = new Menu();
        $menu->add(new MenuItem('Biletlerim', '/my_tickets.php', ['class' => 'secondary']));
        $menu->add(new MenuItem('Firma Paneli', '/company_panel.php', ['class' => 'secondary']));
        $menu->add(new MenuItem('Çıkış Yap', '/logout.php', ['class' => 'button']));
        return $menu;
    }

    public static function buildAdminMenu(): Menu
    {
        $menu = new Menu();
        $menu->add(new MenuItem('Biletlerim', '/my_tickets.php', ['class' => 'secondary']));
        $menu->add(new MenuItem('Admin Paneli', '/admin_panel.php', ['class' => 'secondary']));
        $menu->add(new MenuItem('Çıkış Yap', '/logout.php', ['class' => 'button']));
        return $menu;
    }

    public static function buildGuestMenu(): Menu
    {
        $menu = new Menu();
        $menu->add(new MenuItem('Giriş Yap', '/login.html', ['class' => 'button']));
        $menu->add(new MenuItem('Kayıt Ol', '/register.html', ['class' => 'secondary']));
        return $menu;
    }
}

