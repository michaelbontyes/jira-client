require 'yaml'

dir = File.dirname(File.expand_path(__FILE__))

configValues = YAML.load_file("#{dir}/config.yaml")
data         = configValues['vagrantfile-local']

Vagrant.require_version '>= 1.6.0'


Vagrant.configure('2') do |config|

  config.vm.synced_folder '.', '/vagrant', disabled: false
  config.vm.synced_folder 'wp-content/', '/vagrant/wordpress/wp-content' , disabled: true
  config.vm.synced_folder 'files/', '/vagrant/files' , disabled: true
  config.vm.synced_folder 'puppet/', '/vagrant/puppet/' , disabled: true

  config.vm.box     = "#{data['vm']['box']}"
  config.vm.box_url = "#{data['vm']['box_url']}"

  config.vm.provision "shell",
  inline: "echo Hi,$'\n'Your Wordpress is almost ready!$'\n' "

  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.ignore_private_ip = false
  config.hostmanager.include_offline = true
  config.vm.define "default" do |node|
    node.vm.hostname = "#{data['vm']['hostname']}"
    node.vm.network :private_network, ip: "#{data['vm']['network']['private_network']}"
    node.hostmanager.aliases = %w("#{data['vm']['hostname']}")
  end

  if data['vm']['hostname'].to_s.strip.length != 0
    config.vm.hostname = "#{data['vm']['hostname']}"
  end

  if data['vm']['network']['private_network'].to_s != ''
    config.vm.network 'private_network', ip: "#{data['vm']['network']['private_network']}"
  end



  if !data['vm']['post_up_message'].nil?
    config.vm.post_up_message = "#{data['vm']['post_up_message']}"
  end

  config.vm.usable_port_range = (data['vm']['usable_port_range']['start'].to_i..data['vm']['usable_port_range']['stop'].to_i)

  if data['vm']['chosen_provider'].empty? || data['vm']['chosen_provider'] == 'virtualbox'
    ENV['VAGRANT_DEFAULT_PROVIDER'] = 'virtualbox'

    config.vm.provider :virtualbox do |virtualbox|
      data['vm']['provider']['virtualbox']['modifyvm'].each do |key, value|
        if key == 'memory'
          next
        end
        if key == 'cpus'
          next
        end

        if key == 'natdnshostresolver1'
          value = value ? 'on' : 'off'
        end

        virtualbox.customize ['modifyvm', :id, "--#{key}", "#{value}"]
      end

      virtualbox.customize ['modifyvm', :id, '--memory', "#{data['vm']['memory']}"]
      virtualbox.customize ['modifyvm', :id, '--cpus', "#{data['vm']['cpus']}"]

      if data['vm']['hostname'].to_s.strip.length != 0
        virtualbox.customize ['modifyvm', :id, '--name', config.vm.hostname]
      end
    end
  end

  if data['vm']['chosen_provider'] == 'vmware_fusion' || data['vm']['chosen_provider'] == 'vmware_workstation'
    ENV['VAGRANT_DEFAULT_PROVIDER'] = (data['vm']['chosen_provider'] == 'vmware_fusion') ? 'vmware_fusion' : 'vmware_workstation'

    config.vm.provider 'vmware_fusion' do |v|
      data['vm']['provider']['vmware'].each do |key, value|
        if key == 'memsize'
          next
        end
        if key == 'cpus'
          next
        end

        v.vmx["#{key}"] = "#{value}"
      end

      v.vmx['memsize']  = "#{data['vm']['memory']}"
      v.vmx['numvcpus'] = "#{data['vm']['cpus']}"

      if data['vm']['hostname'].to_s.strip.length != 0
        v.vmx['displayName'] = config.vm.hostname
      end
    end
  end

  if data['vm']['chosen_provider'] == 'parallels'
    ENV['VAGRANT_DEFAULT_PROVIDER'] = 'parallels'

    config.vm.provider 'parallels' do |v|
      data['vm']['provider']['parallels'].each do |key, value|
        if key == 'memsize'
          next
        end
        if key == 'cpus'
          next
        end

        v.customize ['set', :id, "--#{key}", "#{value}"]
      end

      v.memory = "#{data['vm']['memory']}"
      v.cpus   = "#{data['vm']['cpus']}"

      if data['vm']['hostname'].to_s.strip.length != 0
        v.name = config.vm.hostname
      end
    end
  end

  config.vm.provision "shell",
  inline: "echo Provisionning in progress..."

  config.vm.provision :puppet do |puppet|
    puppet.facter = {
      'provisioner_type' => ENV['VAGRANT_DEFAULT_PROVIDER'],
      'vm_target_key'    => 'vagrantfile-local',
    }
    puppet.manifests_path = "#{data['vm']['provision']['puppet']['manifests_path']}"
    puppet.manifest_file  = "#{data['vm']['provision']['puppet']['manifest_file']}"
    puppet.module_path    = "#{data['vm']['provision']['puppet']['module_path']}"
  end

  config.vm.provision "shell",
  inline: "echo  $'\n'The Webserver VM is ready http://webserver.edlfb.net/ $'\n'wp-admin user: admin / pwd: vagrant $'\n'phpMyAdmin user: wordpress / pwd: wordpress $'\n' "

end
